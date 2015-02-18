<!DOCTYPE html>
<html lang="en" ng-app="app" ng-controller="MainCtrl">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">

        <link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon" />
        <link rel="apple-touch-icon" href="/img/apple-touch-icon.png" />
        <link rel="apple-touch-icon" sizes="57x57" href="/img/apple-touch-icon-57x57.png" />
        <link rel="apple-touch-icon" sizes="72x72" href="/img/apple-touch-icon-72x72.png" />
        <link rel="apple-touch-icon" sizes="76x76" href="/img/apple-touch-icon-76x76.png" />
        <link rel="apple-touch-icon" sizes="114x114" href="/img/apple-touch-icon-114x114.png" />
        <link rel="apple-touch-icon" sizes="120x120" href="/img/apple-touch-icon-120x120.png" />
        <link rel="apple-touch-icon" sizes="144x144" href="/img/apple-touch-icon-144x144.png" />
        <link rel="apple-touch-icon" sizes="152x152" href="/img/apple-touch-icon-152x152.png" />

        <title>Wish to Say</title>

        <link rel="stylesheet" href="/css/app.css">
        <link rel="stylesheet" href="/css/jquery.nouislider.css">
        <link rel="stylesheet" href="/css/jquery.nouislider.pips.css">
        <link rel="stylesheet" href="/css/ng-tags-input.css">
        <link href='http://fonts.googleapis.com/css?family=Milonga' rel='stylesheet' type='text/css'>

        <script src="/js/jquery-1.11.2.min.js"></script>
        <script src="/js/angular.min.js"></script>
        <script src="/js/angular-resource.js">
        <script src="/js/jquery.liblink.js"></script>
        <script src="/js/jquery.masonry.min.js"></script>
        <script src="/js/jquery.nouislider.all.js"></script>
        <script src="/js/angular.nouislider.js"></script>
        <script src="/js/angular.masonry.js"></script>
        <script src="/js/sanitize.js"></script>
        <script src="/js/ng-tags-input.js"></script>
        <script src="/js/moment.min.js"></script>
        <script src="/js/app.js"></script>
    </head>
    <body>
        <div class="filter" id="filter">
            <tags-input class="filter-tags" ng-model="searchFilterTags" on-tag-added="controller.addedAttendee($tag)"
                        placeholder="Enter locations, genders, or tags to filter on." replace-spaces-with-dashes="false">
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
            <div class="container" masonry='{ "columnWidth" : ".story", "itemSelector" : ".story" }'>
                <div class="story" masonry-tile ng-repeat="post in posts" ng-model="posts" ng-class="'story-' + post.type">
                    <div class="story-title">
                        <span ng-if="post.type == 'advert'">Advert</span>
                        <span ng-if="post.type != 'advert'">{{ post.title }}</span>
                    </div>
                    <div class="story-text" ng-bind-html="post.te">
                    </div>
                    <div class="story-tags" ng-if="post.type != 'advert'">
                        <button ng-click="addFilter(tag)" class="tag" ng-repeat="tag in post.ta" ng-class="tag.class">
                            {{ tag.te }}
                        </button>
                    </div>
                </div>
            </div>

            <ul class="footer-tabs">
                <li id="footer-tab-faq">F.A.Q</li>
                <li id="footer-tab-privacy">Privacy</li>
                <li id="footer-tab-terms">Terms</li>
                <li id="footer-tab-contact">Contact</li>
                <li id="footer-tab-feed">Feed</li>
            </ul>

            <div class="footer">
                <div style="display: block" id="footer-copy">
                    &copy; 2015
                </div>
                <div style="display: none" id="footer-tab-faq-content">
                    <a name="footer-tab-faq-section"></a>
                    <h2>Frequently Asked Questions</h2>
                    <h3>
                        So, what is this exactly?
                    </h3>
                    <p>
                        wishtosay.com is partially a completely unscientific experiment, and idealistically an equally-unscientific tool &mdash; or at least it could be, but that is up to you. Use (or don't use) the filter options at the top
                        of the screen to find posts of interest, and then [completely anonymously] post your own thoughts or responses if you feel like it. The entire point of this all is to provide
                        a completely open and mostly uncensored space (within legal requirements) where users can say what they want, to anyone they want, anywhere in the world (off-world not yet directly supported,
                        but don't let that stop you from creating new tags identifying alien species and worlds).
                        This means that this site could become an incredible tool for imparting directed snippets of knowledge (anonymously), but it could also become a complete wasteland of typical internet comments and hate. We are hoping for the former, but the latter
                        is entirely plausible as well &mdash; which is where the experimentation bit comes in. We will run this experiment as long as we can (within budget), but if there is some merit to
                        the kind of content that gets posted then we will certainly attempt to prolong it further if possible.
                    </p>
                </div>
                <div style="display: none" id="footer-tab-privacy-content">
                    <a name="footer-tab-privacy-section"></a>
                    <h2>Privacy Policy</h2>
                    <p>
                    </p>
                </div>
                <div style="display: none" id="footer-tab-terms-content">
                    <a name="footer-tab-terms-section"></a>
                    <h2>Terms of Use</h2>
                    <p>
                    </p>
                </div>
                <div style="display: none" id="footer-tab-contact-content">
                    <a name="footer-tab-contact-section"></a>
                    <h2>Contact</h2>
                    <p>

                    </p>
                </div>
                <div style="display: none" id="footer-tab-feed-content">
                    <a name="footer-tab-feed-section"></a>
                    <h2>Feed</h2>
                    <p>
                    </p>
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
                $('#' + id + '-content').hide(400);
                $('#footer-copy').show(400);
            }
            else
            {
                $('ul.footer-tabs li').removeClass('selected');
                $(this).addClass('selected');

                $('.footer div').hide(400);
                $('#' + id + '-content').show(400);
                scrollToAnchor(id + '-section');
            }
        });
    });

    function scrollToAnchor(aid){
        var aTag = $("a[name='"+ aid +"']");
        $('html,body').animate({scrollTop: aTag.offset().top},'slow');
    }
</script>
