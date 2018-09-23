<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../dao/ImageDAO.php';
require_once __DIR__ . '/../dao/ImageRatingDAO.php';

class ImagesController extends Controller {

  private $imageDAO;
  private $imageRatingDAO;

  function __construct() {
    $this->imageDAO = new ImageDAO();
    $this->imageRatingDAO = new ImageRatingDAO();
  }

  public function index() {
    if(!empty($_FILES['image'])) {
      $this->_handleAdd();
    }
    $this->set('images', $this->imageDAO->selectAll());
  }

  public function view() {
    $image = false;
    if(!empty($_GET['id'])) {
      $image = $this->imageDAO->selectById($_GET['id']);
    }
    if(empty($image)) {
      header('Location: index.php');
      exit();
    }
    $canRate = false;
    $images = $this->imageDAO->selectAll();
    $ids = array();
    foreach($images as $timage){
      $ids[] = $timage["id"];
    }
    $current = array_search($image["id"], $ids);
    $next = $current + 1;
    $previous = $current - 1;
    if($next == count($ids)){
      $next = 0;
    }
    if($previous < 0){
      $previous = count($ids)-1;
    }
    $this->set('next', $ids[$next]);
    $this->set('previous', $ids[$previous]);
    if(!empty($_SESSION['user'])) {
      $existingRating = $this->imageRatingDAO->selectByUserAndImageId($_SESSION['user']['id'], $image['id']);
      if(empty($existingRating)) {
        $canRate = true;
      }
    }
    if(!empty($_POST['action'])) {
      if($_POST['action'] == 'Rate' && $canRate) {
        $this->_handleRating($image);
      }
    }

    $this->set('image', $image);
    $this->set('canRate', $canRate);
  }

  private function _handleRating($image) {
    $data = array(
      'user_id' => $_SESSION['user']['id'],
      'image_id' => $image['id'],
      'rating' => $_POST['rating']
    );
    if($this->imageRatingDAO->insert($data)) {
      $_SESSION['info'] = 'Thank you for your rating';
      header('Location: index.php?page=detail&id=' . $image['id']);
      exit();
    }
  }

  private function _handleAdd(){
    if(empty($_SESSION['user'])) {
      $_SESSION['error'] = 'You need to be logged in to add pictures';
      header('Location: index.php');
      exit();
    }
    $data = array_merge($_POST, array(
      'user_id' => $_SESSION['user']['id'],
      'created' => date('Y-m-d H:i:s'),
      'image' => 'will-be-set-later'
    ));
    // valideer de non-file data (gallery_id, title)
    $errors = $this->imageDAO->validate($data);
    if (empty($_FILES['image']) || !empty($_FILES['image']['error'])) {
      $errors['image'] = 'Gelieve een bestand te selecteren';
    }
    if (empty($errors)) {
      // controleer of het een afbeelding is
      $fileinfo = finfo_open(FILEINFO_MIME_TYPE);
      $whitelist_type = array('image/jpeg', 'image/png','image/gif');
      if (!in_array(finfo_file($fileinfo, $_FILES['image']['tmp_name']), $whitelist_type)) {
        $errors['image'] = 'Gelieve een jpeg, png of gif te selecteren';
      }
    }
    if (empty($errors)) {
      // controleer de afmetingen van het bestand
      $size = getimagesize($_FILES['image']['tmp_name']);
      if ($size[0] < 612 || $size[1] < 612) {
        $errors['image'] = 'De afbeelding moet minimum 612x612 pixels groot zijn';
      }
    }
    if (empty($errors)) {
      $projectFolder = realpath(__DIR__ . '/..');
      $targetFolder = $projectFolder . '/assets/img';
      $targetFolder = tempnam($targetFolder, '');
      unlink($targetFolder);
      mkdir($targetFolder, 0777, true);
      $targetFileName = $targetFolder . '/' . $_FILES['image']['name'];
      $this->_resizeAndCrop(
        $_FILES['image']['tmp_name'],
        $targetFileName,
        612, 612
      );
      $relativeFileName = substr($targetFileName, 1 + strlen($projectFolder));
      $data['image'] = $relativeFileName;
      $insertedImage = $this->imageDAO->insert($data);
      if (!empty($insertedImage)) {
        $_SESSION['info'] = 'Het bestand werd ge-upload!';
        header('Location: index.php');
        exit();
      }
    }
    if (!empty($errors)) {
      $_SESSION['error'] = 'De afbeelding kon niet toegevoegd worden!';
    }
    $this->set('errors', $errors);
  }

  private function _resizeAndCrop($src, $dst, $thumb_width, $thumb_height) {
      $type = exif_imagetype($src);
      $allowedTypes = array(
        1,  // [] gif
        2,  // [] jpg
        3,  // [] png
        6   // [] bmp
      );
      if (!in_array($type, $allowedTypes)) {
        return false;
      }
      switch ($type) {
        case 1 :
          $image = imagecreatefromgif($src);
          break;
        case 2 :
          $image = imagecreatefromjpeg($src);
          break;
        case 3 :
          $image = imagecreatefrompng($src);
          break;
        case 6 :
          $image = imagecreatefrombmp($src);
          break;
      }

      $filename = $dst;

      $width = imagesx($image);
      $height = imagesy($image);

      $original_aspect = $width / $height;
      $thumb_aspect = $thumb_width / $thumb_height;

      if ( $original_aspect >= $thumb_aspect ) {
         // If image is wider than thumbnail (in aspect ratio sense)
         $new_height = $thumb_height;
         $new_width = $width / ($height / $thumb_height);
      } else {
         // If the thumbnail is wider than the image
         $new_width = $thumb_width;
         $new_height = $height / ($width / $thumb_width);
      }

      $thumb = imagecreatetruecolor( $thumb_width, $thumb_height );

      // Resize and crop
      imagecopyresampled($thumb,
                         $image,
                         0 - ($new_width - $thumb_width) / 2, // Center the image horizontally
                         0 - ($new_height - $thumb_height) / 2, // Center the image vertically
                         0, 0,
                         $new_width, $new_height,
                         $width, $height);
      imagejpeg($thumb, $filename, 80);
      return true;
    }

}
