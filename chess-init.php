<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <title>Chess-Game</title>
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="./root.css"> <script defer src="./script.js"></script>
    <script defer src="./login/app.js"></script>
    <style>
    body {
      display: flex;
      flex-direction: column;
      align-items: center;
      background-color: #f0f0f0;
      margin: 0;
      height: 100vh;
      justify-content: center;
    }
    canvas {
      border: 2px solid #333;
      cursor: pointer;
    }
    #status {
      margin: 15px;
      font-size: 18px;
      font-weight: bold;
    }
    #promotionDialog {
      display: none;
      background: white;
      border: 2px solid #333;
      padding: 10px;
      border-radius: 6px;
      box-shadow: 0 0 10px rgba(0,0,0,0.3);
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      z-index: 10;
    }
    #promotionDialog button {
      font-size: 20px;
      margin: 5px;
      padding: 5px 12px;
      cursor: pointer;
    }
  </style>
   <link rel="stylesheet" href="./sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body class="background">
    <?php
    include_once('sidebar.php'); // sidebar.js é incluído aqui
    ?>

    <?php
    include_once('./chess.php')
    ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</body>

</html>