<?php
    if (isset($_GET['error'])) {
       echo("Вы должны указать имя и email"); 
    }
?>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Поиск</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container">
      <form method="post" action="actions/order-action">
        <fieldset>
            <div>
              <label for="name">Имя: </label>
              <input name="name" type="text" required>
            </div>
            <div>
              <label for="email">Email: </label>
              <input name="email" type="email" required>
            </div>
            <button type="submit">Оплатить</button>
        </fieldset>
      </form>
    <div>
    </div>
  </div>
</body>
</html>
