;(function ($, window, undefined) {

    'use strict';

    var Base64 = {
        _keyStr: "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",

        encode: function(input) {
            var output = "";
            var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
            var i = 0;

            input = Base64._utf8_encode(input);

            while (i < input.length) {

                chr1 = input.charCodeAt(i++);
                chr2 = input.charCodeAt(i++);
                chr3 = input.charCodeAt(i++);

                enc1 = chr1 >> 2;
                enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
                enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
                enc4 = chr3 & 63;

                if (isNaN(chr2)) {
                    enc3 = enc4 = 64;
                } else if (isNaN(chr3)) {
                    enc4 = 64;
                }

                output = output + this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) + this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);

            }

            return output;
        },


        decode: function(input) {
            var output = "";
            var chr1, chr2, chr3;
            var enc1, enc2, enc3, enc4;
            var i = 0;

            input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

            while (i < input.length) {

                enc1 = this._keyStr.indexOf(input.charAt(i++));
                enc2 = this._keyStr.indexOf(input.charAt(i++));
                enc3 = this._keyStr.indexOf(input.charAt(i++));
                enc4 = this._keyStr.indexOf(input.charAt(i++));

                chr1 = (enc1 << 2) | (enc2 >> 4);
                chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
                chr3 = ((enc3 & 3) << 6) | enc4;

                output = output + String.fromCharCode(chr1);

                if (enc3 != 64) {
                    output = output + String.fromCharCode(chr2);
                }
                if (enc4 != 64) {
                    output = output + String.fromCharCode(chr3);
                }

            }

            output = Base64._utf8_decode(output);

            return output;

        },

        _utf8_encode: function(string) {
            string = string.replace(/\r\n/g, "\n");
            var utftext = "";

            for (var n = 0; n < string.length; n++) {

                var c = string.charCodeAt(n);

                if (c < 128) {
                    utftext += String.fromCharCode(c);
                }
                else if ((c > 127) && (c < 2048)) {
                    utftext += String.fromCharCode((c >> 6) | 192);
                    utftext += String.fromCharCode((c & 63) | 128);
                }
                else {
                    utftext += String.fromCharCode((c >> 12) | 224);
                    utftext += String.fromCharCode(((c >> 6) & 63) | 128);
                    utftext += String.fromCharCode((c & 63) | 128);
                }

            }

            return utftext;
        },

        _utf8_decode: function(utftext) {
            var string = "";
            var i = 0;
            var c = 0, c1 = 0, c2 = 0, c3 = 0;

            while (i < utftext.length) {

                c = utftext.charCodeAt(i);

                if (c < 128) {
                    string += String.fromCharCode(c);
                    i++;
                }
                else if ((c > 191) && (c < 224)) {
                    c2 = utftext.charCodeAt(i + 1);
                    string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
                    i += 2;
                }
                else {
                    c2 = utftext.charCodeAt(i + 1);
                    c3 = utftext.charCodeAt(i + 2);
                    string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
                    i += 3;
                }

            }

            return string;
        }

    }

    var app = window.app = angular.module('app', ['ngResource', 'ngTagsInput', 'ngSanitize', 'nouislider', 'masonry']);

    app.factory('SearchClient', ['$resource', function($resource) {
        return $resource('/search/:controllerMethod/:methodQuery',
            {
                controllerMethod: "@controllerMethod",
                methodQuery: "@methodQuery"
            },
            {
                autocomplete: {
                    method: "GET",
                    params: {
                        controllerMethod: "autocomplete",
                        methodQuery: "@methodQuery"
                    },
                    isArray: true
                }
            }
        );
    }]);

    app.factory('PostClient', ['$resource', function($resource) {
        return $resource('/post/:controllerMethod/:methodQuery',
            {
                controllerMethod: "@controllerMethod",
                methodQuery: "@methodQuery"
            },
            {
                create: {
                    method: "POST",
                    params: {
                        controllerMethod: "create",
                        methodQuery: "@methodQuery"
                    },
                    isArray: true
                },
                load: {
                    method: "GET",
                    params: {
                        controllerMethod: "load",
                        methodQuery: "@methodQuery"
                    },
                    isArray: false
                }
            }
        );
    }]);

    app.controller('MainCtrl', function ($q, $scope, SearchClient, PostClient) {
        $scope.urlHash = "";

        $scope.age = {'from': 15, 'to': 35};

        $scope.searchFilterTags = [
            /*
            { text: 'Tag1', class: 'tag-ethnicity', id: 3, type: 'location' },
            { text: 'Tag2', class: 'tag-location' },
            { text: 'Tag3'},
            { text: 'Tafsdfdsf ,sdf3', class: 'tag' }
            */
        ];

        $scope.posts = [];

        $scope.doPost = function() {

            var data = this.createFilter();
            data.te = $scope.post;

            PostClient.create(data,
                function success(result) {
                    $scope.posts.unshift($scope.loadPost(result[0]));
                    $scope.post = "";
                    $("#post").val("");
                    $scope.adaptPostFooter();
                },
                function error(result) {
                    console.log(result);
                }
            );
        }

        $scope.loadPosts = function() {

            PostClient.load({methodQuery: $scope.urlHash.substr(1)},
                function success(data) {
                    $scope.posts = [];

                    if (data['c'] != undefined)
                    {
                        if (data['c'] > 0 && data['p'] != undefined)
                        {
                            $.each(data['p'], function(index, post) {
                                $scope.posts.push($scope.loadPost(post));
                            });
                        }
                        else
                        {
                            // no posts
                            $scope.posts = [];
                        }
                    }
                },
                function error() {
                }
            );
        }

        $scope.loadPost = function(post) {
            if (post.type == undefined)
            {
                post.type = 'normal';
            }

            post.title = moment.unix(post.ti).fromNow();

            if (post['ta'] != undefined)
            {
                $.each(post['ta'], function(tagindex, tag) {
                    switch (tag['ty'])
                    {
                        case 'continent':
                        case 'country':
                        case 'subdivision':
                        case 'city':
                            tag.class = 'tag-location';
                            break;

                        case 'gender':
                            tag.class = 'tag-gender'
                            break;

                        default:
                            tag.class = '';
                            break;
                    }
                });
            }

            return post;
        }

        $scope.filterToggled = false;

        $scope.addFilter = function(tag) {
            var found = false;

            $.each($scope.searchFilterTags, function(index, searchTag) {
                if(tag.ty == searchTag.type && tag.i == searchTag.id)
                {
                    found = true;
                }
            });

            if (!found)
            {
                var newTag = $scope.createValidTag(tag)

                if (newTag !== false) {
                    $scope.searchFilterTags.push(newTag);
                }
            }
        }

        $scope.updateUrlHash = function() {
            var data = $scope.createFilter();
            var newHash = Base64.encode(JSON.stringify(data));

            if (newHash != $scope.urlHash) {
                $scope.urlHash = newHash;
                document.location.hash = newHash;
            }
        }

        window.onhashchange = function(){
            $scope.checkHash();
        }

        $scope.checkHash = function() {
            var hash = document.location.hash;

            if (hash != $scope.urlHash) {
                // first decode and see if it's valid
                try {
                    var data = JSON.parse(Base64.decode(hash));

                    if ($scope.setFilter(data)) {
                        $scope.urlHash = hash;
                        $scope.loadPosts();
                    }
                } catch (e) {
                    return;
                }
            }
        }

        $scope.setFilter = function (data) {
            var changed = false;

            // check for valid fields
            if (data.t != undefined && $.isArray(data.t)) {
                $scope.searchFilterTags = [];

                $.each(data.t, function(index, tag){
                    $scope.addFilter(tag);
                });
            }

            if (data.a != undefined) {
                if (data.a.f != undefined &&
                    data.a.t != undefined &&
                    data.a.f >= 0 && data.a.f <= 100 &&
                    data.a.t >= 0 && data.a.t <= 100 &&
                    data.a.t >= data.a.f
                ) {
                    $scope.age = { from: data.a.f, to: data.a.t };
                    changed = true;
                }
            }

            return changed;
        }

        $scope.createValidTag = function(tag) {
            var newTag = {};
            var valid = false;

            if (tag.te != undefined) {
                valid = true;
                newTag.text = tag.te;
            }

            newTag.id = tag.i != undefined ? tag.i : 0;
            newTag.type = tag.ty != undefined ? tag.ty : 'tag';

            if (tag.c != undefined)
            {
                newTag.class = tag.c;
            }
            else
            {
                switch (newTag.type)
                {
                    case 'continent':
                    case 'country':
                    case 'subdivision':
                    case 'city':
                        newTag.class = 'tag-location';
                        break;

                    case 'gender':
                        newTag.class = 'tag-gender'
                        break;

                    default:
                        newTag.class = '';
                        break;
                }
            }

            if (valid)
            {
                return newTag;
            }

            return false;
        }
        
        $scope.createFilter = function() {
            var data = {
                t: [],
                a: {f:$scope.age.from, t:$scope.age.to }
            };

            $.each($scope.searchFilterTags, function(index, tag){
                data.t.push({
                    te: tag.text,
                    i: tag.id != undefined ? tag.id : 0,
                    ty: tag.type != undefined ? tag.type : 'tag'
                });
            });

            return data;
        }

        $scope.loadTags = function(query) {
            var deferred = $q.defer();

            SearchClient.autocomplete({methodQuery: query},
                function success(data) {
                    deferred.resolve(data);
                },
                function error() {
                }
            );

            return deferred.promise;
        };

        $scope.toggleFilter = function() {
            var filter = $("#filter");
            var top = 0;

            if(!$scope.filterToggled)
            {
                top = -filter.height() + 5;
            }

            var headerMargin = filter.height() + top + 35;

            filter.animate({top: top + "px"}, 400, function() {
                $scope.filterToggled = !$scope.filterToggled;

                if($scope.filterToggled)
                {
                    $("#filter-toggle").html('&#9660;');
                }
                else
                {
                    $("#filter-toggle").html('&#9650;');
                }
            });

            $(".header").animate({margin: headerMargin + "px 0 0 0"}, 400, function() {});
        }

        $scope.adaptPostFooter = function() {
            var postInput = $("#post");
            var footer = $(".footer");

            var maxHeight = $(window).height() / 3;

            postInput.height(0);
            var scrollVal = postInput[0].scrollHeight - 16;

            if(scrollVal > maxHeight)
            {
                postInput.height(maxHeight);
                footer.css('padding-bottom', maxHeight + 30);
                return
            }

            postInput.height(scrollVal);
            footer.css('padding-bottom', scrollVal + 30);
        }

        $scope.parseWindowSize = function() {
            // change height of header image as necessary
            var filter = $("#filter");

            if($scope.filterToggled)
            {
                // ensure that filter is completely out of screen, but not too far out
                filter.css('top', -filter.height() + 5 + "px");
            }
            else
            {
                filter.css('top', 0);
            }

            var header = $(".header");
            var margin = filter.height() + filter.position().top + 35;
            header.css('margin-top', margin + 'px');
        }

        window.onload = function() {
            $scope.checkHash();
            $scope.parseWindowSize();

            $scope.$watch('[age.from, age.to]', function () {
                // update filter
                $scope.updateUrlHash();
                $scope.parseWindowSize();
            });

            $scope.$watch('searchFilterTags', function() {
                // update filter
                $scope.updateUrlHash();
                $scope.parseWindowSize();
            }, true);
        }

        $( window ).resize(function() {
            $scope.parseWindowSize();
        });
    });

})(jQuery, this);