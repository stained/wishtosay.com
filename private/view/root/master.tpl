<!DOCTYPE html>
<html lang="en" ng-app="app" ng-controller="MainCtrl">
    <head>
        <meta charset="utf-8">
        <meta name="google-site-verification" content="fvH2Hi8DUYO0f0kwPs5vd3n6KQ3WEkicBsQLNPLmLEs" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=0">

        <meta name="description" content="">
        <meta name="author" content="">

        <link rel="apple-touch-icon" sizes="57x57" href="/img/ico/apple-touch-icon-57x57.png">
        <link rel="apple-touch-icon" sizes="60x60" href="/img/ico/apple-touch-icon-60x60.png">
        <link rel="apple-touch-icon" sizes="72x72" href="/img/ico/apple-touch-icon-72x72.png">
        <link rel="apple-touch-icon" sizes="76x76" href="/img/ico/apple-touch-icon-76x76.png">
        <link rel="apple-touch-icon" sizes="114x114" href="/img/ico/apple-touch-icon-114x114.png">
        <link rel="apple-touch-icon" sizes="120x120" href="/img/ico/apple-touch-icon-120x120.png">
        <link rel="apple-touch-icon" sizes="144x144" href="/img/ico/apple-touch-icon-144x144.png">
        <link rel="apple-touch-icon" sizes="152x152" href="/img/ico/apple-touch-icon-152x152.png">
        <link rel="apple-touch-icon" sizes="180x180" href="/img/ico/apple-touch-icon-180x180.png">
        <link rel="icon" type="image/png" href="/img/ico/favicon-32x32.png" sizes="32x32">
        <link rel="icon" type="image/png" href="/img/ico/favicon-194x194.png" sizes="194x194">
        <link rel="icon" type="image/png" href="/img/ico/favicon-96x96.png" sizes="96x96">
        <link rel="icon" type="image/png" href="/img/ico/android-chrome-192x192.png" sizes="192x192">
        <link rel="icon" type="image/png" href="/img/ico/favicon-16x16.png" sizes="16x16">
        <link rel="manifest" href="/img/ico/manifest.json">
        <link rel="shortcut icon" href="/img/ico/favicon.ico">
        <meta name="msapplication-TileColor" content="#f55e3b">
        <meta name="msapplication-TileImage" content="/img/ico/mstile-144x144.png">
        <meta name="msapplication-config" content="/img/ico/browserconfig.xml">
        <meta name="theme-color" content="#ffffff">

        <title>Wish to Say</title>

        <link rel="stylesheet" href="/css/all.min.css">
        <script src="/js/all.min.js"></script>

        <link href='http://fonts.googleapis.com/css?family=Open+Sans:700,300,400' rel='stylesheet' type='text/css'>
    </head>
    <body>

        <div class="loading" style="display: none">
            <div class="loader"></div>
            Please stand by for just a moment.
        </div>

        <div class="error" style="display: none">
            <div class="error-icon"></div>
            <div class="error-text"></div>
        </div>

        <div class="filter" id="filter">
            <tags-input class="filter-tags" ng-model="searchFilterTags" on-tag-added="controller.addedAttendee($tag)"
                        placeholder="Filter by..." replace-spaces-with-dashes="false">
                <auto-complete source="loadTags($query)"></auto-complete>
            </tags-input>
            <div class="age-filter">
                <div slider ng-from="age.from" ng-to="age.to" start=0 end=100 step=1>
                    <div class="age-filter-baby-icon">
                    </div>
                    <div class="age-filter-elderly-icon">
                    </div>
                </div>
            </div>
            <div class="filter-toggle" id="filter-toggle" ng-click="toggleFilter()">
                &#9650;
            </div>
        </div>

        <div class="header">
        </div>

        <div class="content">

            <div class="story-none" ng-show="!posts.length">
                <h2>Nothing here yet</h2>
                Try changing the age and tag filters above, or post something of your own. You can also tap the button below for a random selection (at own risk).<br />
                <button class="load-random" ng-click="loadRandom()">I wish to see something random!</button>
            </div>

            <div class="outer-container">
                <div class="container" masonry='{ "columnWidth" : ".story", "itemSelector" : ".story" }'>
                    <div class="story" masonry-tile ng-repeat="post in posts" ng-model="posts" ng-class="'story-' + post.type">
                        <div class="story-title">
                            <span ng-if="post.type == 'advert'">Advert</span>
                            <span ng-if="post.type != 'advert'">{{ post.title }}</span>
                        </div>
                        <div class="story-text" ng-bind-html="post.te">
                        </div>
                        <div class="story-tags" ng-if="post.type != 'advert'">
                            <button ng-click="addFilter(tag)" class="tag" ng-repeat="tag in post.ta" ng-class="tag.tclass">
                                {{ tag.te }}
                            </button>
                        </div>
                        <div class="story-report">
                            <a href="javascript: void()" ng-click="reportPost(post)">Report</a>
                        </div>
                    </div>
                </div>

                <button class="load-more" style="display: none" ng-click="loadPosts(false)">Load More</button>
            </div>

            <ul class="footer-tabs">
                <li id="footer-tab-faq">F.A.Q</li>
                <li id="footer-tab-privacy">Privacy</li>
                <li id="footer-tab-terms">Terms</li>
                <li id="footer-tab-contact">Contact</li>
            </ul>

            <div class="footer">
                <div style="display: block" id="footer-copy">
                    &copy; 2015 Goats on a Rope. All rope reserved for goats.
                </div>
                <div style="display: none" id="footer-tab-faq-content">
                    <a name="footer-tab-faq-section"></a>
                    <h2>Frequently Asked Questions</h2>
                    <h3>
                        Where are the questions?
                    </h3>
                    <p>
                        We're waiting for a couple to be frequently <a href="mailto:i.really@wishtosay.com?subject=future FAQ">asked</a>.
                    </p>
                </div>
                <div style="display: none" id="footer-tab-privacy-content">
                    <a name="footer-tab-privacy-section"></a>
                    <h2>Privacy Policy</h2>
                    <p>
                        No personal identifying information is stored on this server. No logs are kept, and no login service is provided. A mathematical hash of
                        partially identifying information (browser useragent and IP address) is stored along with the post for statistical purposes, but cannot
                        be reversed without access to the original data (which we do not store).
                    </p>
                </div>
                <div style="display: none" id="footer-tab-terms-content">
                    <a name="footer-tab-terms-section"></a>
                    <h2>Terms of Use</h2>
                    <p>
                        Posts are made at your own discretion, but may be removed at ours. In general we won't censor posts, but will be forced to remove them if we receive too many complaints.
                    </p>
                </div>
                <div style="display: none" id="footer-tab-contact-content">
                    <a name="footer-tab-contact-section"></a>
                    <h2>Contact</h2>
                    <p>
                        Comments, requests, and questions can be sent to <a href="mailto:i.really@wishtosay.com">i.really@wishtosay.com</a>. Hate mail will automatically be forwarded
                        to your local kitten sanctuary. Do you really want the kittens to think that you hate them?
                    </p>
                </div>
            </div>
        </div>

        <div class="footer-post">
            <textarea rows="1" id="post" placeholder="I wish to say..." ng-model="post" ng-keyup="adaptPostFooter()" ng-change="adaptPostFooter()"></textarea>
            <button ng-click="doPost()"><img src="/img/logosmall.png" /></button>
        </div>

    </body>
</html>

<script type="text/javascript">

    $(document).ready(function()
    {
        $('ul.footer-tabs li').click(function(e)
        {
            var id = $(this)[0].id;

            if($(this).hasClass('selected'))
            {
                $(this).removeClass('selected');
                $('#' + id + '-content').slideToggle(400);
                $('#footer-copy').slideToggle(400);
            }
            else
            {
                $('ul.footer-tabs li').removeClass('selected');
                $(this).addClass('selected');

                $('.footer div:visible').slideToggle(400);
                $('#' + id + '-content').slideToggle(400);
                scrollToAnchor(id + '-section');
            }
        });
    });

    function scrollToAnchor(aid){
        var aTag = $("a[name='"+ aid +"']");
        $('html,body').animate({scrollTop: aTag.offset().top},'slow');
    }
</script>
