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
        <div class="filter">
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
            <div class="footer">
                <a href="javascript: null" id="footer-what-link">What is this?</a> |
                <a href="javascript: null" id="footer-privacy-link">Privacy Policy</a> |
                <a href="javascript: null" id="footer-terms-link">Terms of Use</a> |
                <a href="javascript: null" id="footer-contact-link">Contact</a>

                <div style="display: none" id="footer-what">
                    <a name="footer-what-section"></a>
                    <h2>What is this?</h2>
                    wishtosay.com is partially a completely unscientific experiment, and idealistically an equally-unscientific tool &mdash; or at least it could be, but that is up to you. Use (or don't use) the filter options at the top
                    of the screen to find posts of interest, and then [completely anonymously] post your own thoughts or responses if you feel like it. The entire point of this all is to provide
                    a completely open and mostly uncensored space (within legal requirements) where users can say what they want, to anyone they want, anywhere in the world (off-world not yet directly supported,
                    but don't let that stop you from creating new tags identifying alien species and worlds).
                    This means that this site could become an incredible tool for imparting directed snippets of knowledge (anonymously), but it could also become a complete wasteland of typical internet comments and hate. We are hoping for the former, but the latter
                    is entirely plausible as well &mdash; which is where the experimentation bit comes in. We will run this experiment as long as we can (within budget), but if there is some merit to
                    the kind of content that gets posted then we will certainly attempt to prolong it further if possible.
                </div>

                <div style="display: none" id="footer-privacy">
                    <h2>Privacy Policy</h2><a name="footer-privacy-section"></a>
                    No intentional uniquely identifying information is stored anywhere on this service (no post identifiers, no access logs, nothing!). This does imply
                    that there are some limitations in providing this service, as well as the (very likely) possibility of "doxing" (posting of personal information without
                    the express permission of the owner of said information) to occur; since posting personal information goes against the idea of anonimity on this service (and it's a real shitty thing to do) we will be forced to permanently remove such posts on request.
                    This also means that we can't provide any edit/delete post capabilities, or anything that requires some level of unique user identification. Our wish is to
                    provide a platform where anyone can say anything without the possibility of a direct attempt at retribution.
                </div>

                <div style="display: none" id="footer-terms">
                    <h2>Terms of Use</h2><a name="footer-terms-section"></a>
                    There are none (at this point). Use this platform in a way that makes sense to you, and if it turns out that
                    a majority-use pattern emerges (and we are able to continue paying for the service), then we will (maybe) try to optimize
                    toward that pattern. That said (there is always a but), we cannot (at this time) afford to fight battles based on what someone has written, so if we do receive DMCA (or similarly-official looking, smelling, or tasting) requests
                    then we will be forced to remove the posts in question.
                    <br /><br />
                    In an attempt to provide some level of user-moderated decency for this experiment we allows users (like yourself) to report a post. When a post has a certain number of reports it will
                    automatically be demoted in the feed (to practical invisibility). This can, of course, be abused (given our lack of personal identification anywhere), but that is to be expected. If you post
                    something and see that it has been removed (and it really means something to you or someone else) then feel free to re-post it (with the understanding that it could again be automatically demoted).
                    At the same time if you don't agree with something someone said then either ignore, or report it (which will make the post vanish from your own feed for the duration of the session), best
                    use your own intuition and common sense in this regard.
                </div>

                <div style="display: none" id="footer-contact">
                    <h2>Contact</h2><a name="footer-contact-section"></a>
                    All queries, requests, and/or hate mail can be sent to <a href="mailto:i.really@wishtosay.com">i.really@wishtosay.com</a>
                </div>
            </div>
        </div>

        <div class="footer-post">
            <textarea rows="1" id="post" placeholder="I wish to say..." ng-model="post"></textarea>
            <button ng-click="doPost()"><img src="/img/logosmall.png" /></button>
        </div>

    </body>
</html>

<script type="text/javascript">
    $("#footer-what-link").click(function(){
        $("#footer-terms").hide();
        $("#footer-privacy").hide();
        $("#footer-what").show();
        $("#footer-contact").hide();
        scrollToAnchor('footer-what-section');
    });

    $("#footer-privacy-link").click(function(){
        $("#footer-terms").hide();
        $("#footer-privacy").show();
        $("#footer-what").hide();
        $("#footer-contact").hide();
        scrollToAnchor('footer-privacy-section');
    });

    $("#footer-terms-link").click(function(){
        $("#footer-terms").show();
        $("#footer-privacy").hide();
        $("#footer-what").hide();
        $("#footer-contact").hide();
        scrollToAnchor('footer-terms-section');
    });

    $("#footer-contact-link").click(function(){
        $("#footer-terms").hide();
        $("#footer-privacy").hide();
        $("#footer-what").hide();
        $("#footer-contact").show();
        scrollToAnchor('footer-contact-section');
    });

    $("#post").keyup(function (e) {
        adaptiveheight(this);
    });

    $("#post").change(function () {
        adaptiveheight(this);
    });

    i=0;
    j=0;

    function scrollToAnchor(aid){
        var aTag = $("a[name='"+ aid +"']");
        $('html,body').animate({scrollTop: aTag.offset().top},'slow');
    }

    function adaptiveheight(a) {

        var maxHeight = $(window).height() / 3;

        $(a).height(0);
        var scrollval = $(a)[0].scrollHeight;

        if(scrollval > maxHeight)
        {
            $(a).height(maxHeight);
            $(".footer").css('padding-bottom', maxHeight + 30);
            return
        }

        $(a).height(scrollval);
        $(".footer").css('padding-bottom', scrollval + 30);

        if (parseInt(a.style.height) > $(window).height()) {
            if(j==0){
                max=a.selectionEnd;
            }
            j++;
            var i =a.selectionEnd;
            console.log(i);
            if(i >=max){
                $(document).scrollTop(parseInt(a.style.height));
            }else{
                $(document).scrollTop(0);
            }
        }
    }
</script>
