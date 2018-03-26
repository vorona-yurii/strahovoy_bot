<?php
/**
 * Created by PhpStorm.
 * User: yuv
 * Date: 25.03.2018
 * Time: 2:06
 */
require_once "../config.php";
require_once "../function.php";

$users = OrderFullUser();

if(isset($_GET['delete'])){
    OrderDeleteUser($_GET['delete']);
    header("Location: index.php");
}

?>
<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Админ - панель телеграмм бота</title>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.css" rel="stylesheet">

    <!-- Morris -->
    <link href="css/plugins/morris/morris-0.4.3.min.css" rel="stylesheet">

    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">

</head>

<body>
<div id="wrapper">
    <nav class="navbar-default navbar-static-side" role="navigation">
        <div class="sidebar-collapse">
            <ul class="nav" id="side-menu">
                <li class="nav-header">
                    <div class="dropdown profile-element">
                        <span>
                            <img alt="image" class="img-circle" src="img/profile_small.jpg" />
                        </span>
                        <span class="block m-t-xs" style="color: #fff;">
                            <strong class="font-bold">Админ сайта</strong>
                        </span>
                    </div>
                </li>
                <li>
                    <a href="coefficient.php"><i class="fa fa-cog"></i> <span class="nav-label">Общий коф</span></a>
                </li>
                <li>
                    <a href="fondy.php"><i class="fa fa-cog"></i> <span class="nav-label">Fondy</span></a>
                </li>
                <li>
                    <a href="bulk_mailing.php"><i class="fa fa-arrow-right"></i> <span class="nav-label">Массовая рассылка</span></a>
                </li>
            </ul>

        </div>
    </nav>

    <div id="page-wrapper" class="gray-bg">
        <div class="wrapper wrapper-content">
            <div class="row">

                <div class="col-lg-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>Таблица юзеров</h5>
                        </div>
                        <div class="ibox-content">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Емейл</th>
                                        <th>Телефон</th>
                                        <th>День рождение</th>
                                        <th>Мир</th>
                                        <th>Дата отъезда</th>
                                        <th>Дата приезда</th>
                                        <th>Отдых или работа</th>
                                        <th>Тариф</th>
                                        <th>Гражд. отв.</th>
                                        <th>Багаж</th>
                                        <th>Цена</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($users as $user){?>
                                    <tr>
                                        <th><?=$user['id']?></th>
                                        <td><?=$user['email']?></td>
                                        <td><?=$user['phone']?></td>
                                        <td><?=$user['bithday']?></td>
                                        <td><?=$user['world']?></td>
                                        <td><?=$user['date_to']?></td>
                                        <td><?=$user['date_back']?></td>
                                        <td><?=$user['work_recreation']?></td>
                                        <td><?=$user['tarif']?></td>
                                        <td><?=$user['civil']?></td>
                                        <td><?=$user['baggage']?></td>
                                        <td><?=round($user['total_price'], 2)?> грн</td>
                                        <td><a href="index.php?delete=<?=$user['id']?>">Удалить</a></td>
                                    </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>


        <div class="footer">
            <div>
                <strong>Copyright</strong> yuv.com.ua &copy; 2018
            </div>
        </div>

    </div>
</div>

<!-- Mainly scripts -->
<script src="js/jquery-2.1.1.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

</body>
</html>

