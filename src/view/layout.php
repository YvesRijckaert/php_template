<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="author" content="Yves Rijckaert">
    <meta name="description" content="PHP template">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $title; ?></title>
    <?php echo $css; ?>
  </head>
  <body>
    <header>
      <nav>
        <ul>
          <li class="menuitem <?php if($currentPage == 'home') echo ' menuitem--active';?>"><a href="index.php">home</a></li>
          <li class="menuitem <?php if($currentPage == 'about') echo ' menuitem--active';?>"><a href="index.php?page=about">about</a></li>
          <li class="menuitem <?php if($currentPage == 'items') echo ' menuitem--active';?>"><a href="index.php?page=items">items</a></li>
        </ul>
      </nav>
      <?php
        if (!empty($_SESSION['error'])) {
          echo '<div class="error box">' . $_SESSION['error'] . '</div>';
        }
        if (!empty($_SESSION['info'])) {
          echo '<div class="info box">' . $_SESSION['info'] . '</div>';
        }
      ?>
    </header>
    <section class="login-container">
      <?php if (empty($_SESSION['user'])): ?>
        <header class="hidden">
          <h1>Login</h1>
        </header>
        <form class="login-form" method="post" action="index.php?page=login">
          <input type="hidden" name="action" value="login" />
          <div class="input-container text">
            <label>
              <span class="form-label hidden">Email:</span>
              <input type="text" autocomplete="on" name="email" placeholder="email" class="form-input" />
            </label>
          </div>
          <div class="input-container text">
            <label>
              <span class="form-label hidden">Password:</span>
              <input type="password" autocomplete="on" name="password" placeholder="password" class="form-input" />
            </label>
          </div>
          <div class="input-container submit">
              <button type="submit" class="form-submit">Login</button> or <a href="index.php?page=register">Register</a>
          </div>
        </form>
      <?php else: ?>
        <header class="hidden">
          <h1>Logout</h1>
        </header>
        <p><?php echo $_SESSION['user']['email'];?> - <a href="index.php?page=logout" class="logout-button">Logout</a></p>
      <?php endif; ?>
    </section>
    <?php echo $content; ?>
    <?php echo $js; ?>
  </body>
</html>
