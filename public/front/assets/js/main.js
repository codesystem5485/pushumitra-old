
    jQuery('.dropdown-icon').on('click', function() {
        jQuery(this).toggleClass('active').next('ul').slideToggle();
        jQuery(this).parent().siblings().children('ul').slideUp();
        jQuery(this).parent().siblings().children('.active').removeClass('active');
    });
    window.addEventListener('scroll', function() {
        const header = document.querySelector('header.style-1, header.style-2, header.style-3, header.style-4, header.style-5');
        header.classList.toggle("sticky", window.scrollY > 0);
    });
    $('.sidebar-button').on("click", function() {
        $('.main-menu').addClass('show-menu');
    });
    $('.menu-close-btn').on("click", function() {
        $('.main-menu').removeClass('show-menu');
    });
 

   
    
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  
    // $('.popup-youtube').magnificPopup({
    //     type: 'iframe'
    // });
    // $('[data-fancybox="gallery"]').fancybox({
    //     buttons: ["slideShow", "thumbs", "zoom", "fullScreen", "share", "close"],
    //     loop: false,
    //     protect: true
    // });


    // Images upload script Start Here
    
    jQuery(document).ready(function () {
        ImgUpload();
        });
    
        function ImgUpload() {
        var imgWrap = "";
        var imgArray = [];
    
        $('.upload__inputfile').each(function () {
            $(this).on('change', function (e) {
            imgWrap = $(this).closest('.upload__box').find('.upload__img-wrap');
            var maxLength = $(this).attr('data-max_length');
    
            var files = e.target.files;
            var filesArr = Array.prototype.slice.call(files);
            var iterator = 0;
            filesArr.forEach(function (f, index) {
    
                if (!f.type.match('image.*')) {
                return;
                }
    
                if (imgArray.length > maxLength) {
                return false
                } else {
                var len = 0;
                for (var i = 0; i < imgArray.length; i++) {
                    if (imgArray[i] !== undefined) {
                    len++;
                    }
                }
                if (len > maxLength) {
                    return false;
                } else {
                    imgArray.push(f);
    
                    var reader = new FileReader();
                    reader.onload = function (e) {
                    var html = "<div class='upload__img-box'><div style='background-image: url(" + e.target.result + ")' data-number='" + $(".upload__img-close").length + "' data-file='" + f.name + "' class='img-bg'><div class='upload__img-close'></div></div></div>";
                    imgWrap.append(html);
                    iterator++;
                    }
                    reader.readAsDataURL(f);
                }
                }
            });
            });
        });
    
        $('body').on('click', ".upload__img-close", function (e) {
            var file = $(this).parent().data("file");
            for (var i = 0; i < imgArray.length; i++) {
            if (imgArray[i].name === file) {
                imgArray.splice(i, 1);
                break;
            }
            }
            $(this).parent().parent().remove();
        });
        }

        // Images upload script End Here

