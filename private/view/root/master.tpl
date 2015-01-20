<!DOCTYPE html>
<html lang="en" ng-app="app" ng-controller="Root">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" href="/img/favicon.ico">

        <title>Wish to Say</title>

        <link rel="stylesheet" href="/css/app.css">
        <link rel="stylesheet" href="/css/jquery.nouislider.css">
        <link rel="stylesheet" href="/css/jquery.nouislider.pips.css">
        <link href='http://fonts.googleapis.com/css?family=Milonga' rel='stylesheet' type='text/css'>

        <script src="/js/jquery-1.11.2.min.js"></script>
        <script src="/js/angular.min.js"></script>
        <script src="/js/jquery.liblink.js"></script>
        <script src="/js/jquery.masonry.min.js"></script>
        <script src="/js/jquery.nouislider.all.js"></script>
        <script src="/js/angular.nouislider.js"></script>
        <script src="/js/app.js"></script>
        <script src="/js/parallax.js"></script>
    </head>
    <body>
        <div class="filter">
            <input type="text" name="filter" placeholder="Enter locations, genders, or tags to filter on." />
            <div class="ageFilter">
                <div slider ng-from="age.from" ng-to="age.to" start=0 end=100 step=1></div>
            </div>
        </div>

        <div class="header parallax-window" data-parallax="scroll" data-image-src="/img/header.jpg" data-win-min-height=195>
            <div class="header-wish">
                What do you Wish To Say ...
            </div>
            <div class="header-wish-subtitle">
                to anyone, anytime, and anywhere on earth?
            </div>
            <div class="header-content-email">
                Personalized stories to your inbox. Sign up below.
                <div class="margin-10">
                    <input type="text" name="email" placeholder="Enter your email address" />
                    <button>Subscribe Now</button>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="container">
                <?php
                $stories = array(
                "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.",
                "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc imperdiet ipsum felis, vitae molestie leo blandit sit amet. Sed molestie pellentesque augue sit amet pellentesque. Ut in maximus lectus, eu dictum diam. Nunc nec euismod tellus. Etiam eget malesuada quam. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Etiam sed magna finibus est vestibulum pretium. Morbi pulvinar mauris felis, id vestibulum enim bibendum eu. Sed commodo sapien et est eleifend, sed fringilla dolor finibus. Quisque nisi ipsum, consequat sed lorem eget, accumsan auctor nunc. Mauris ultrices at odio sit amet tincidunt. Duis pulvinar odio purus, sit amet molestie metus tristique et. Maecenas eget orci in erat finibus cursus. Quisque varius ligula ut aliquet finibus. Praesent rhoncus tellus in nunc aliquam, non vehicula dui finibus.<br /><br />Pellentesque diam augue, aliquet vel pulvinar sit amet, posuere ac orci. Aliquam nec neque sit amet dolor mollis condimentum ut vitae orci. Morbi congue eros a lorem lacinia egestas. Pellentesque ut cursus sem, eget ornare elit. Morbi molestie auctor sem. Fusce eget porttitor ligula. Pellentesque cursus nisl eget est bibendum, eget vestibulum nibh ullamcorper. In rhoncus congue purus ut hendrerit. Sed condimentum, dolor ut porta pharetra, libero mi pulvinar mi, nec congue libero turpis vel turpis. Vestibulum dictum urna dolor, vel volutpat lacus lobortis ut. Sed sodales quam eget ligula ornare feugiat a a nisl. Phasellus a congue enim. Donec tristique tempor nisl, et interdum nulla faucibus ac. Curabitur porttitor nisl ut vestibulum egestas. Donec viverra magna lacinia laoreet egestas.",
                "Ut mollis lectus at ullamcorper iaculis. Etiam in efficitur sem. Suspendisse potenti. Fusce tristique quis mauris ut sollicitudin. Morbi in lorem vitae justo fringilla lacinia. Aenean ac eros elit. Nulla viverra consequat blandit. Fusce condimentum est sodales, molestie massa vulputate, rhoncus ante. Nullam ut tempor lacus, at malesuada ligula.<br /><br />Integer leo enim, aliquet sed magna vitae, suscipit pretium diam. Nunc quis neque quam. Donec porta nunc pretium diam fringilla, quis tempus velit fermentum. Donec scelerisque finibus eleifend. Donec fringilla varius auctor. In porta convallis fermentum. Nam sollicitudin vestibulum interdum. Sed sollicitudin nulla nisi, eu mollis ex viverra iaculis. Aenean at lorem non lectus fringilla lobortis eu sed arcu. Integer pretium quam vitae porta feugiat."
                );
                for($i = 0; $i < 30; $i++) {
                ?>
                <div class="story<?php echo ($i == 2 ? ' story-featured': ($i % 10 == 0 ? ' story-advert' : '')); ?>">
                    <div class="story-title">Story <?php echo $i; ?></div>
                    <div class="story-text">
                        <?php echo $stories[rand(0, 3)]; ?>
                    </div>
                    <div class="story-tags">
                        <div class="tag tag-location">Here, Some Place</div>
                        <div class="tag tag-gender">Male Horse</div>
                        <div class="tag">Fishes</div>
                        <div class="tag">Cows</div>
                        <div class="tag tag-ethnicity">Blue</div>
                    </div>
                </div>
                <?php
                }
                ?>
            </div>
            <div class="footer">
                <div class="footer-content-email">
                    Personalized stories to your inbox. Sign up below.
                    <div class="margin-10">
                        <input type="text" name="email" placeholder="Enter your email address" />
                        <button>Subscribe Now</button>
                    </div>
                </div>
                <p>
                    <a href="/site/terms">Terms &amp; Conditions</a> |
                    <a href="/site/privacy">Privacy Policy</a> |
                    <a href="/site/faq">FAQ</a> |
                    <a href="/site/feed">Feed</a> |
                    <a href="/site/contact">Contact</a>
                    <br />
                    <span class="copy">Copyright &copy; 2015. All rights reserved.</span>
                </p>
            </div>
        </div>

        <script>
            $(function(){

                var $container = $('.container');

                $container.masonry({
                    columnWidth: 1,
                    itemSelector : '.story'
                });

            });
        </script>

    </body>
</html>
