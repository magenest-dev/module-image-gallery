define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'mage/url'
], function ($, modal,urlBuilder) {
    'use strict';

    return function (config) {

        var list_image = config.list_image;
        for (var i = 0; i < 8; i++)
            list_image.shift();

        var list_image_popup = config.list_image_popup;
        var checkIfLessThan3;
        var checkLogin = config.checkLogin;
        var loginUrl = "window.location.href =" + config.loginUrl;

        $('.tablinks').change(function () {
            $('.widget-timeline').remove();

            list_image_popup = $.ajax({
                url: config.getListImageUrl,
                type: 'POST',
                async: false,
                dataType: 'json',
                data: {
                    gallery_id: $(this).children("option:selected").val(),
                },
                success: function (response) {
                    if (response[0]['image_id'] == null)
                        response.shift();
                },
                error: function () {
                }
            }).responseJSON;

            list_image = $.ajax({
                url: config.getListImageUrl,
                type: 'POST',
                async: false,
                dataType: 'json',
                data: {
                    gallery_id: $(this).children("option:selected").val(),
                },
                success: function (response) {
                    $('.gallery_description').html(response[0]['gallery_description']);
                    if (response[0]['layout_type'] == 0)
                        var layout_type = '1';
                    else
                        var layout_type = 'gallery_grid';
                    if (!$('#instagram-type').hasClass(layout_type)) {
                        $('#instagram-type').removeClass("1");
                        $('#instagram-type').removeClass("gallery-grid");
                        if (layout_type == '1')
                            $('#instagram-type').addClass("1");
                        else $('#instagram-type').addClass("gallery-grid");
                    }

                    if (response[0]['image_id'] == null)
                        response.shift();

                    if (response.length < 8) {
                        for (var i = 0; i < response.length; i++) {
                            var imageUrl = config.imageUrl + response[i].image;
                            var imageId = response[i].image_id;
                            var imageLove = response[i].love;
                            var color = response[i].color;
                            var product_id = response[i].product_id;
                            var word;
                            if (imageLove > 1)
                                 word = '<span class="word">likes</span>';
                            else word = '<span class="word">like</span>';

                            appendImage(imageUrl,imageId,imageLove,color,product_id,word);
                        }
                        checkIfLessThan3 = 1;
                    } else {
                        for (var i = 0; i < 8; i++) {
                            var imageUrl = config.imageUrl + response[0].image;
                            var imageId = response[0].image_id;
                            var imageLove = response[0].love;
                            var color = response[0].color;
                            var product_id = response[0].product_id;
                            var word;
                            if (imageLove > 1)
                                word = '<span class="word">likes</span>';
                            else word = '<span class="word">like</span>';

                            appendImage(imageUrl,imageId,imageLove,color,product_id,word);

                            response.shift();
                        }
                    }
                },
                error: function () {
                    console.log('fail');
                }
            }).responseJSON;

            if (checkIfLessThan3 == 1) {
                list_image = [];
                checkIfLessThan3 = 0;
            }
        });

        function showImageWheel(deltaY) {
            if (deltaY > 0 && ($(window).scrollTop() > $(document).height() - $(window).height() - $('.page-footer').height() - $('.copyright').height())) {

                if (list_image.length < 4) {
                    for (var i = 0; i < list_image.length; i++) {
                        var imageUrl = config.imageUrl + list_image[i].image;
                        var imageId = list_image[i].image_id;
                        var imageLove = list_image[i].love;
                        var color = list_image[i].color;
                        var product_id = list_image[i].product_id;
                        var word;
                        if (imageLove > 1)
                            word = '<span class="word">likes</span>';
                        else word = '<span class="word">like</span>';

                        appendImage(imageUrl,imageId,imageLove,color,product_id,word);
                    }
                    list_image = [];

                } else {

                    for (var i = 0; i < 4; i++) {
                        var imageUrl = config.imageUrl + list_image[0].image;
                        var imageId = list_image[0].image_id;
                        var imageLove = list_image[0].love;
                        var color = list_image[0].color;
                        var product_id = list_image[0].product_id;
                        var word;
                        if (imageLove > 1)
                            word = '<span class="word">likes</span>';
                        else word = '<span class="word">like</span>';

                        appendImage(imageUrl,imageId,imageLove,color,product_id,word);

                        list_image.shift();
                    }
                }
            }
        }

        function appendImage(imageUrl,imageId,imageLove,color,product_id,word)
        {
            $('#instagram-type').append('<div class="widget-timeline" id="image_' + imageId + '">\n' +
                '            <div class="widget-entry-container">\n' +
                '                <div class="widget-timeline-entry ' + config.hoverEffect + '"\n' +
                '                     style="cursor: pointer; background-image: url(\' ' + imageUrl + ' \');">\n' +
                '                    <div class="widget-text-container">\n' +
                '                        <div class="widget-service-icon"><i class="fa fa-heart love-icon" style="color: ' + color + '"></i>\n' +
                '                        </div>\n' +
                '                        <div class="widget-timeline-text">\n' +
                '                           <div class="widget-entry-title"><span class="love-number">' + imageLove + '</span> ' + word + '</div>\n' +
                '                        </div>\n' +
                '                    </div>\n' +
                '                </div>\n' +
                '            </div>\n' +
                '                    <div class="widget-text-container">\n' +
                '                        <div class="widget-service-icon"><i class="fa fa-heart love-icon-res" style="color: ' + 'red' + '"></i>\n' +
                '                        </div>\n' +
                '                        <div class="widget-timeline-text">\n' +
                '                           <div class="card-footer">' +
                '                               <div class="widget-entry-title"><span class="love-number">' + imageLove + '</span> ' + word + '</div>\n' +
                '                           </div>'+
                '                        </div>\n' +
                '                        <div class="viewproduct-btn">\n' +
                '                           <a href="#"> View Product</a>\n' +
                '                        </div>' +
                '                    </div>\n' +
                '        <input type="hidden" value="' + imageId + '" class="image_id">\n' +
                '        <input type="hidden" value="' + product_id + '" class="product_id">\n' +
                '        </div>');

            if (color == 'white')
                $('#image_' + imageId).find('.love-icon-res').addClass('fa-heart-o');
        }

        $(window).on('wheel touchmove', function (event) {
            if(event.originalEvent.type == "wheel")
                showImageWheel(event.originalEvent.deltaY);
            else
            {
                var touch = event.originalEvent.touches[0];
                showImageWheel(touch.pageY);
            }
        });

        //show popup
        var options = {
            type: 'popup',
            responsive: true,
            innerScroll: true,
            buttons: []
        };

        var popup = modal(options, $('#popup-modal'));
        var chosen_image_index;
        var index_slideshow;

        var image_id_request = config.image_id_request;

        if (image_id_request != '') {
            $('.widget-timeline-entry').css('display', 'none');
            var index_of_image_id;
            for (var i = 0; i < list_image_popup.length; i++) {
                if (list_image_popup[i].image_id == image_id_request) {
                    index_of_image_id = i;
                    $('.image-title').text(list_image_popup[index_of_image_id].title);
                    $('.image-description').text(list_image_popup[index_of_image_id].description);
                    if (list_image_popup[index_of_image_id].product_id == null) {
                        $('.popup-product_id').val('null');
                        $('.popup-link-product').text('');
                    } else {
                        $('.popup-product_id').val(list_image_popup[index_of_image_id].product_id);
                        $('.popup-link-product').text(list_image_popup[index_of_image_id].product_name);
                        $('.popup-link-product').attr('href', list_image_popup[index_of_image_id].product_link);
                    }

                    if (list_image_popup[index_of_image_id].color == "white")
                        $('.popup-love-icon').css('color', 'black');
                    else
                        $('.popup-love-icon').css('color', list_image_popup[index_of_image_id].color);
                    $('.popup-love-number').text(list_image_popup[index_of_image_id].love);
                    $('.current-index').text(index_of_image_id + 1);
                    $('.total-images').text(list_image_popup.length);
                    $('.popup-image-current').attr('src', config.imageUrl + list_image_popup[index_of_image_id].image);
                    $('.popup-image_id').val(image_id_request);
                    if (list_image_popup[index_of_image_id].love > 1)
                        $('.popup-word').text('likes');
                    else
                        $('.popup-word').text('like');
                    break;
                }
            }

            //image for small slide-show
            $('.image-slide-show').attr('src', '');
            $('.image-slide-show').removeClass('border-for-image');
            var list_image_slideshow = [];
            index_slideshow = parseInt(index_of_image_id / 9);
            chosen_image_index = parseInt(index_of_image_id % 9);

            if (index_of_image_id == 0)
                index_slideshow = parseInt(0);
            if (index_of_image_id == list_image_popup.length - 1 && parseInt(index_of_image_id % 9) == 0)
                index_slideshow = parseInt(index_of_image_id / 9);

            for (var i = index_slideshow * 9; i < index_slideshow * 9 + 9; i++) {
                if (typeof list_image_popup[i] !== 'undefined')
                    list_image_slideshow.push(list_image_popup[i]);
            }

            for (var i = 0; i < list_image_slideshow.length; i++) {
                $('#slideshow' + i).attr('src', config.imageUrl + list_image_slideshow[i].image);
                if (i == chosen_image_index) {
                    $('#slideshow' + i).addClass('border-for-image');
                }

            }

            $('#popup-modal').modal('openModal');
            $(window).off('wheel touchmove');
        }

        $('#instagram-type').on('click', '.widget-timeline-entry', function () {
            var background = $(this).closest('.widget-timeline-entry').css("background-image");
            background = background.replace(/.*\s?url\([\'\"]?/, '').replace(/[\'\"]?\).*/, '');

            $('.popup-image-current').attr('src', background);
            $('.popup-image_id').val($(this).closest('.widget-timeline').find('.image_id').val());

            var index_of_image_id;
            for (var i = 0; i < list_image_popup.length; i++) {
                if (list_image_popup[i].image_id == $('.popup-image_id').val()) {
                    index_of_image_id = i;
                    $('.image-title').text(list_image_popup[index_of_image_id].title);
                    $('.image-description').text(list_image_popup[index_of_image_id].description);
                    if (list_image_popup[index_of_image_id].product_id == null) {
                        $('.popup-product_id').val('null');
                        $('.popup-link-product').text('');
                    } else {
                        $('.popup-product_id').val(list_image_popup[index_of_image_id].product_id);
                        $('.popup-link-product').text(list_image_popup[index_of_image_id].product_name);
                        $('.popup-link-product').attr('href', list_image_popup[index_of_image_id].product_link);
                    }
                    if (list_image_popup[index_of_image_id].color == "white")
                        $('.popup-love-icon').css('color', 'black');
                    else
                        $('.popup-love-icon').css('color', list_image_popup[index_of_image_id].color);
                    $('.popup-love-number').text(list_image_popup[index_of_image_id].love);
                    $('.current-index').text(index_of_image_id + 1);
                    $('.total-images').text(list_image_popup.length);
                    if (list_image_popup[index_of_image_id].love > 1)
                        $('.popup-word').text('likes');
                    else
                        $('.popup-word').text('like');
                    window.history.pushState("", "", config.urlKey+"?gallery_id=" + $('.tablinks').val() + '&image_id=' + $('.popup-image_id').val() );
                    break;
                }
            }

            //image for small slide-show
            $('.image-slide-show').attr('src', '');
            $('.image-slide-show').removeClass('border-for-image');
            var list_image_slideshow = [];
            index_slideshow = parseInt(index_of_image_id / 9);
            chosen_image_index = parseInt(index_of_image_id % 9);

            if (index_of_image_id == 0)
                index_slideshow = parseInt(0);
            if (index_of_image_id == list_image_popup.length - 1 && parseInt(index_of_image_id % 9) == 0)
                index_slideshow = parseInt(index_of_image_id / 9);

            for (var i = index_slideshow * 9; i < index_slideshow * 9 + 9; i++) {
                if (typeof list_image_popup[i] !== 'undefined')
                    list_image_slideshow.push(list_image_popup[i]);
            }

            for (var i = 0; i < list_image_slideshow.length; i++) {
                $('#slideshow' + i).attr('src', config.imageUrl + list_image_slideshow[i].image);
                if (i == chosen_image_index) {
                    $('#slideshow' + i).addClass('border-for-image');
                }

            }

            $('#popup-modal').modal('openModal');
            $(window).off('wheel touchmove');
        });

        $('#instagram-type').on('click','.viewproduct-btn',function () {
            $(this).closest('.widget-timeline').find('.widget-timeline-entry').trigger('click');
        });

        //popup share button
        var url;
        var image;
        $('.popup-facebook-share').click(function () {
            url = config.urlKey + '?gallery_id=' + $('.tablinks').val() + '&image_id=' + $('.popup-image_id').val();
            window.open('https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(url), "", "width=500,height=400");
        });

        $('.popup-twitter-share').click(function () {
            url = config.urlKey + '?gallery_id=' + $('.tablinks').val() + '&image_id=' + $('.popup-image_id').val();
            window.open('https://twitter.com/intent/tweet?url=' + encodeURIComponent(url), "", "width=500,height=400");
        });

        $('.popup-pinterest-share').click(function () {
            image = $('.popup-image-current').attr('src');
            url = config.urlKey + '?image_id=' + $('.popup-image_id').val() + '&gallery_id=' + $('.tablinks').val();
            window.open('http://pinterest.com/pin/create/link/?url=' + encodeURIComponent(url) + '&media=' + image, "", "width=500,height=400");
        });

        //popup love icon
        $('.popup-love-icon').click(function () {
            if (checkLogin == 1)
            {
                var lovenumber = $.ajax({
                    url: config.loveUrl,
                    type: 'POST',
                    showLoader: true,
                    async: false,
                    dataType: 'json',
                    data: {
                        image_id: $('.popup-image_id').val(),
                    },
                    success: function (response) {
                    },
                    error: function () {
                    }
                }).responseJSON;

                $('.popup-love-number').html(lovenumber['number_love']);
                var word;
                if (lovenumber['number_love'] > 1)
                    word = "likes";
                else word = "like";
                $('.popup-word').text(word);

                $('#image_' + $('.popup-image_id').val()).find('.fa-heart').css('color', lovenumber['color']);
                $('#image_' + $('.popup-image_id').val()).find('.love-number').text(lovenumber['number_love']);
                $('#image_' + $('.popup-image_id').val()).find('.word').text(word);

                if (lovenumber['color'] == "white")
                {
                    $('.popup-love-icon').css('color', 'black');
                    $('#image_' + $('.popup-image_id').val()).find('.love-icon-res').addClass('fa-heart-o');
                    $('#image_' + $('.popup-image_id').val()).find('.love-icon-res').css('color','red');
                }
                else
                {
                    $('.popup-love-icon').css('color', lovenumber['color']);
                    $('#image_' + $('.popup-image_id').val()).find('.love-icon-res').removeClass('fa-heart-o');
                }

                for (var i = 0; i < list_image_popup.length; i++) {
                    if (list_image_popup[i].image_id == $('.popup-image_id').val()) {
                        list_image_popup[i].love = lovenumber['number_love'];
                        list_image_popup[i].color = lovenumber['color'];
                    }
                }
            }
            else
            {
                var url = urlBuilder.build('imagegallery/gallery/redirectLogin');
                window.location.href = url;
            }
        });

        //love-icon responsive
        $('#instagram-type').on('click','.love-icon-res',function () {
            if (checkLogin == 0)
                {
                    var url = urlBuilder.build('imagegallery/gallery/redirectLogin');
                    window.location.href = url;
                }
            else
            {
                var image_id = $(this).closest('.widget-timeline').find('.image_id').val();
                var lovenumber = $.ajax({
                    url: config.loveUrl,
                    type: 'POST',
                    showLoader: true,
                    async: false,
                    dataType: 'json',
                    data: {
                        image_id: image_id,
                    },
                    success: function (response) {
                    },
                    error: function () {
                    }
                }).responseJSON;

                var word;
                if (lovenumber['number_love'] > 1)
                    word = "likes";
                else word = "like";

                $('#image_' + image_id).find('.fa-heart').css('color', lovenumber['color']);
                $('#image_' + image_id).find('.love-number').text(lovenumber['number_love']);
                $('#image_' + image_id).find('.word').text(word);

                if (lovenumber['color'] == "white")
                {
                    $('#image_' + image_id).find('.love-icon-res').addClass('fa-heart-o');
                    $('#image_' + image_id).find('.love-icon-res').css('color','red');
                }
                else
                {
                    $('#image_' + image_id).find('.love-icon-res').removeClass('fa-heart-o');
                }

                for (var i = 0; i < list_image_popup.length; i++) {
                    if (list_image_popup[i].image_id == image_id) {
                        list_image_popup[i].love = lovenumber['number_love'];
                        list_image_popup[i].color = lovenumber['color'];
                    }
                }
            }
        });

        //change to previous image
        $('.previous-button,.previous-slide').click(function () {
            var index_of_image_id;
            for (var i = 0; i < list_image_popup.length; i++) {
                if (list_image_popup[i].image_id == $('.popup-image_id').val()) {
                    if (i == 0)
                        index_of_image_id = list_image_popup.length - 1;
                    else
                        index_of_image_id = i - 1;
                    $('.popup-image-current').attr('src', config.imageUrl + list_image_popup[index_of_image_id].image);

                    $('.image-title').text(list_image_popup[index_of_image_id].title);
                    $('.image-description').text(list_image_popup[index_of_image_id].description);
                    if (list_image_popup[index_of_image_id].product_id == null) {
                        $('.popup-product_id').val('null');
                        $('.popup-link-product').text('');
                    } else {
                        $('.popup-product_id').val(list_image_popup[index_of_image_id].product_id);
                        $('.popup-link-product').text(list_image_popup[index_of_image_id].product_name);
                        $('.popup-link-product').attr('href', list_image_popup[index_of_image_id].product_link);
                    }

                    if (list_image_popup[index_of_image_id].color == "white")
                        $('.popup-love-icon').css('color', 'black');
                    else
                        $('.popup-love-icon').css('color', list_image_popup[index_of_image_id].color);

                    $('.popup-love-number').text(list_image_popup[index_of_image_id].love);
                    $('.popup-image_id').val(list_image_popup[index_of_image_id].image_id);
                    if (list_image_popup[index_of_image_id].love > 1)
                        $('.popup-word').text('likes');
                    else
                        $('.popup-word').text('like');

                    window.history.pushState("", "", config.urlKey+"?gallery_id=" + $('.tablinks').val() + '&image_id=' + $('.popup-image_id').val() );
                    break;
                }
            }
            chosen_image_index--;
            if ((chosen_image_index == -1 && index_of_image_id != list_image_popup.length - 1) || index_of_image_id == list_image_popup.length - 1) {
                chosen_image_index = 8;
                if (index_of_image_id == list_image_popup.length - 1) {
                    chosen_image_index = parseInt((list_image_popup.length - 1) % 9);
                    index_slideshow = parseInt((list_image_popup.length - 1) / 9);
                } else
                    index_slideshow--;

                var j = 0;
                for (var i = index_slideshow * 9; i < index_slideshow * 9 + 9; i++) {
                    if (typeof list_image_popup[i] !== 'undefined') {
                        $('#slideshow' + j).attr('src', config.imageUrl + list_image_popup[i].image);
                        j++;
                    } else {
                        $('#slideshow' + j).attr('src', '');
                        j++;
                    }
                }
            }

            $('.image-slide-show').removeClass('border-for-image');
            $('#slideshow' + chosen_image_index).addClass('border-for-image');
            $('.current-index').text(index_of_image_id + 1);
        });

        $('.next-button,.next-slide').click(function () {
            var index_of_image_id;
            for (var i = 0; i < list_image_popup.length; i++) {
                if (list_image_popup[i].image_id == $('.popup-image_id').val()) {
                    if (i == list_image_popup.length - 1)
                        index_of_image_id = 0;
                    else
                        index_of_image_id = i + 1;
                    $('.popup-image-current').attr('src', config.imageUrl + list_image_popup[index_of_image_id].image);

                    $('.image-title').text(list_image_popup[index_of_image_id].title);
                    $('.image-description').text(list_image_popup[index_of_image_id].description);
                    if (list_image_popup[index_of_image_id].product_id == null) {
                        $('.popup-product_id').val('null');
                        $('.popup-link-product').text('');
                    } else {
                        $('.popup-product_id').val(list_image_popup[index_of_image_id].product_id);
                        $('.popup-link-product').text(list_image_popup[index_of_image_id].product_name);
                        $('.popup-link-product').attr('href', list_image_popup[index_of_image_id].product_link);
                    }

                    if (list_image_popup[index_of_image_id].color == "white")
                        $('.popup-love-icon').css('color', 'black');
                    else
                        $('.popup-love-icon').css('color', list_image_popup[index_of_image_id].color);

                    $('.popup-love-number').text(list_image_popup[index_of_image_id].love);
                    $('.popup-image_id').val(list_image_popup[index_of_image_id].image_id);
                    if (list_image_popup[index_of_image_id].love > 1)
                        $('.popup-word').text('likes');
                    else
                        $('.popup-word').text('like');

                    window.history.pushState("", "", config.urlKey+"?gallery_id=" + $('.tablinks').val() + '&image_id=' + $('.popup-image_id').val() );
                    break;
                }
            }
            chosen_image_index++;
            if ((chosen_image_index % 9 == 0 && index_of_image_id != 0) || index_of_image_id == 0) {
                chosen_image_index = 0;
                if (index_of_image_id == 0)
                    index_slideshow = 0;
                else
                    index_slideshow++;
                var j = 0;
                for (var i = index_slideshow * 9; i < index_slideshow * 9 + 9; i++) {
                    if (typeof list_image_popup[i] !== 'undefined') {
                        $('#slideshow' + j).attr('src', config.imageUrl + list_image_popup[i].image);
                        j++;
                    } else {
                        $('#slideshow' + j).attr('src', '');
                        j++;
                    }
                }
            }

            $('.image-slide-show').removeClass('border-for-image');
            $('#slideshow' + chosen_image_index).addClass('border-for-image');
            $('.current-index').text(index_of_image_id + 1);
        });

        $('.image-slide-show').unbind().click(function () {
            if ($(this).attr('src') != '') {
                $('.image-slide-show').removeClass('border-for-image');
                $(this).addClass('border-for-image');
                for (var i = index_slideshow * 9; i < index_slideshow * 9 + 9; i++) {
                    if (typeof list_image_popup[i] !== 'undefined') {
                        var image_url = config.imageUrl + list_image_popup[i].image;
                        if (image_url == $(this).attr('src')) {
                            chosen_image_index = i % 9;
                            index_of_image_id = i;
                            $('.current-index').text(index_of_image_id + 1);

                            $('.popup-image-current').attr('src', image_url);
                            $('.popup-image_id').val(list_image_popup[i].image_id);
                            $('.image-title').text(list_image_popup[i].title);
                            $('.image-description').text(list_image_popup[i].description);
                            if (list_image_popup[i].product_id == null) {
                                $('.popup-product_id').val('null');
                                $('.popup-link-product').text('');
                            } else {
                                $('.popup-product_id').val(list_image_popup[index_of_image_id].product_id);
                                $('.popup-link-product').text(list_image_popup[index_of_image_id].product_name);
                                $('.popup-link-product').attr('href', list_image_popup[index_of_image_id].product_link);
                            }

                            if (list_image_popup[i].color == "white")
                                $('.popup-love-icon').css('color', 'black');
                            else
                                $('.popup-love-icon').css('color', list_image_popup[i].color);
                            $('.popup-love-number').text(list_image_popup[i].love);
                            if (list_image_popup[i].love > 1)
                                $('.popup-word').text('likes');
                            else
                                $('.popup-word').text('like');

                            window.history.pushState("", "", config.urlKey+"?gallery_id=" + $('.tablinks').val() + '&image_id=' + $('.popup-image_id').val() );
                            break;
                        }
                    }
                }
            }
        });

        $(document).on('keydown', function (e) {
            switch (e.keyCode) {
                case 27:
                    $('#popup-modal').modal('closeModal');
                    return;
                case 37:
                    $('.previous-slide').trigger('click');
                    return;
                case 39:
                    $('.next-slide').trigger('click');
                    return;
            }
        });

        $('#popup-modal').on('modalclosed', function () {
            window.history.pushState("", "", config.urlKey);
            $(window).on('wheel touchmove', function (event) {
                if(event.originalEvent.type == "wheel")
                    showImageWheel(event.originalEvent.deltaY);
                else
                {
                    var touch = event.originalEvent.touches[0];
                    showImageWheel(touch.pageY);
                }
            });
        });

        var gallery_id_request = config.gallery_id_request;

        if (gallery_id_request != '')
            $('.tablinks').val(gallery_id_request);
        else {
            $('.tablinks').val(0);
        }

        //share all image
        $('.popup-facebook-share-all').click(function () {
            url = config.urlKey + "?gallery_id=" + $('.tablinks').val();
            window.open('https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(url), "", "width=500,height=400");
        });

        $('.popup-twitter-share-all').click(function () {
            url = config.urlKey + "?gallery_id=" + $('.tablinks').val();
            window.open('https://twitter.com/intent/tweet?url=' + encodeURIComponent(url), "", "width=500,height=400");
        });

        $('.popup-pinterest-share-all').click(function () {
            url = config.urlKey + "?gallery_id=" + $('.tablinks').val();
            window.open('https://www.pinterest.com/pin/find/?url=' + encodeURIComponent(url), "", "width=500,height=400");
        });

        $(document).on('click','.modals-overlay',function () {
            var magento_version = config.magento_version;
            magento_version = magento_version.substring(0,3);
            if (magento_version <= 2.2)
                $('#popup-modal').modal('closeModal');
        });
    }
});