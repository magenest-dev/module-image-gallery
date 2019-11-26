define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'mage/url'
], function ($, modal,urlBuilder) {
    'use strict';

    return function (config) {
        //show popup
        var options = {
            type: 'popup',
            responsive: true,
            innerScroll: true,
            buttons: []
        };

        var list_image_popup;
        var list_image_popup_top = config.list_image_popup_top;
        var list_image_popup_footer = config.list_image_popup_footer;
        var position;
        var default_url = window.location.href;

        $('.imageslider').click(function () {
            switch ($(this).closest('.image-item').find('.position_slider').val()) {
                case "top":
                    list_image_popup = list_image_popup_top;
                    position = 'top';
                    break;
                case "footer":
                    list_image_popup = list_image_popup_footer;
                    position = 'footer';
                    break;
                default:
                    list_image_popup = config.list_image_popup;
            }
        });

        var popup = modal(options, $('#popup-modal'));
        var chosen_image_index;
        var index_slideshow;

        $('.imageslider').click(function () {
            var background = $(this).attr('src');
            $('.popup-image-current').attr('src', background);
            $('.popup-image_id').val($(this).closest('.image-item').find('.image_id').val());


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

                    changeUrlWhenClickImage($('.popup-image_id').val());

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
                $('#slideshow' + i).attr('src', config.mediaUrl + list_image_slideshow[i].image);
                if (i == chosen_image_index) {
                    $('#slideshow' + i).addClass('border-for-image');
                }

            }

            $('#popup-modal').modal('openModal');
        });

        //popup share button
        var url;
        var image;
        $('.popup-facebook-share').click(function () {
            switch (position) {
                case "top":
                    url = config.urlKey + '?gallery_id=' + config.gallery_id_category_top + '&image_id=' + $('.popup-image_id').val();
                    break;
                case "footer":
                    url = config.urlKey + '?gallery_id=' + config.gallery_id_category_footer + '&image_id=' + $('.popup-image_id').val();
                    break;
                default:
                    url = config.urlKey + '?gallery_id=' + config.gallery_id_product + '&image_id=' + $('.popup-image_id').val();
            }

            window.open('https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(url), "", "width=500,height=400");
        });

        $('.popup-twitter-share').click(function () {
            switch (position) {
                case "top":
                    url = config.urlKey + '?gallery_id=' + config.gallery_id_category_top + '&image_id=' + $('.popup-image_id').val();
                    break;
                case "footer":
                    url = config.urlKey + '?gallery_id=' + config.gallery_id_category_footer + '&image_id=' + $('.popup-image_id').val();
                    break;
                default:
                    url = config.urlKey + '?gallery_id=' + config.gallery_id_product + '&image_id=' + $('.popup-image_id').val();
            }

            window.open('https://twitter.com/intent/tweet?url=' + encodeURIComponent(url), "", "width=500,height=400");
        });

        $('.popup-pinterest-share').click(function () {
            image = $('.popup-image-current').attr('src');

            switch (position) {
                case "top":
                    url = config.urlKey + '?gallery_id=' + config.gallery_id_category_top + '&image_id=' + $('.popup-image_id').val();
                    break;
                case "footer":
                    url = config.urlKey + '?gallery_id=' + config.gallery_id_category_footer + '&image_id=' + $('.popup-image_id').val();
                    break;
                default:
                    url = config.urlKey + '?gallery_id=' + config.gallery_id_product + '&image_id=' + $('.popup-image_id').val();
            }

            window.open('http://pinterest.com/pin/create/link/?url=' + encodeURIComponent(url) + '&media=' + image, "", "width=500,height=400");
        });

        //popup love icon
        $('.popup-love-icon').click(function () {
            if (config.checkLogin == 0)
            {
                var url = urlBuilder.build('imagegallery/gallery/redirectLogin');
                window.location.href = url;
            }
            else
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

                if (lovenumber['color'] == "white")
                    $('.popup-love-icon').css('color', 'black');
                else
                    $('.popup-love-icon').css('color', lovenumber['color']);


                for (var i = 0; i < list_image_popup.length; i++) {
                    if (list_image_popup[i].image_id == $('.popup-image_id').val()) {
                        list_image_popup[i].love = lovenumber['number_love'];
                        list_image_popup[i].color = lovenumber['color'];
                    }
                }

                switch (position) {
                    case "top":
                        for (var i = 0; i < list_image_popup_footer.length; i++)
                            if (list_image_popup_footer[i].image_id == $('.popup-image_id').val())
                            {
                                list_image_popup_footer[i].love = lovenumber['number_love'];
                                list_image_popup_footer[i].color = lovenumber['color'];
                            }
                        break;
                    case "footer":
                        for (var i = 0; i < list_image_popup_top.length; i++)
                            if (list_image_popup_top[i].image_id == $('.popup-image_id').val())
                            {
                                list_image_popup_top[i].love = lovenumber['number_love'];
                                list_image_popup_top[i].color = lovenumber['color'];
                            }
                        break;
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
                    $('.popup-image-current').attr('src', config.mediaUrl + list_image_popup[index_of_image_id].image);

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

                    changeUrlWhenClickImage($('.popup-image_id').val());

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
                        $('#slideshow' + j).attr('src', config.mediaUrl + list_image_popup[i].image);
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

        $('.next-button,.next-slide').unbind().click(function () {
            var index_of_image_id;
            for (var i = 0; i < list_image_popup.length; i++) {
                if (list_image_popup[i].image_id == $('.popup-image_id').val()) {
                    if (i == list_image_popup.length - 1)
                        index_of_image_id = 0;
                    else
                        index_of_image_id = i + 1;
                    $('.popup-image-current').attr('src', config.mediaUrl + list_image_popup[index_of_image_id].image);

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

                    changeUrlWhenClickImage($('.popup-image_id').val());

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
                        $('#slideshow' + j).attr('src', config.mediaUrl + list_image_popup[i].image);
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
                var index_of_image_id;
                $('.image-slide-show').removeClass('border-for-image');
                $(this).addClass('border-for-image');
                for (var i = index_slideshow * 9; i < index_slideshow * 9 + 9; i++) {
                    if (typeof list_image_popup[i] !== 'undefined') {
                        var image_url = config.mediaUrl + list_image_popup[i].image;
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

                            changeUrlWhenClickImage($('.popup-image_id').val());

                            break;
                        }
                    }
                }
            }
        });

        function changeUrlWhenClickImage(image_id)
        {
            var url = '';
            switch (position) {
                case "top":
                    url = config.urlKey+"?gallery_id=" + config.gallery_id_category_top + "&image_id=" + image_id;
                    break;
                case "footer":
                    url = config.urlKey+"?gallery_id=" + config.gallery_id_category_footer + "&image_id=" + image_id;
                    break;
                default:
                    url = config.urlKey+"?gallery_id=" + config.gallery_id_product + "&image_id=" + image_id;
            }
            window.history.pushState("", "", url );
        }

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

        $(document).on('click','.modals-overlay',function () {
            var magento_version = config.magento_version;
            magento_version = magento_version.substring(0,3);
            if (magento_version <= 2.2)
                $('#popup-modal').modal('closeModal');
        });

        $('#popup-modal').on('modalclosed', function() {
            window.history.pushState("", "", default_url );
        });
    }
});