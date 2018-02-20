var ClassPopup = function()
{
    var selector_wrap,
        submit_selector,
        open_selector,
        submit_path;

    function load(oInitData)
    {
        if (!oInitData || !oInitData.selector_wrap || !oInitData.submit_selector || !oInitData.submit_path
            || !oInitData.open_selector)
        {
            return false;
        }

        selector_wrap = oInitData.selector_wrap;
        submit_selector = oInitData.submit_selector;
        submit_path = oInitData.submit_path;
        open_selector = oInitData.open_selector;

        return true;
    }
    function resetForm()
    {
        $(selector_wrap).find('.has_error').removeClass('has_error').end().find('.error_message').remove();
    }
    function successCallback(reply)
    {
        var jThis, k;

        resetForm();

        if (reply.toaster) {
            toaster({error: !reply.success, text: reply.toaster});
        }

        if(reply.success) {
            window.location.reload();
        } else {
            for(k in reply.error) {

                if ('image_id' == k) {
                    continue;
                }

                jThis = $(selector_wrap).find('[name*="' + k + '"]');
                jThis.closest('.wrap_field').addClass('has_error')
                    .append('<div class="error_message">' + reply.error[k] + '<br></div>');
            }
        }
    }
    function submit(e)
    {
        var oData = {};

        $(selector_wrap).find("input[name]").each(function() {
            if ("radio" == this.type) {
                if (this.checked) {
                    oData[this.name] = this.value;
                }
                return;
            }

            oData[this.name] = this.value;
        });

        $.ajax({
            url: submit_path,
            type: 'post',
            dataType: 'json',
            data: oData,
            success: successCallback
        });

    }
    function show(e)
    {
        resetForm();
        $('.modal').modal('hide');
        $(selector_wrap).modal('show');
    }
    function init(oConfigInit)
    {
        if (!load(oConfigInit)) {
            throw new Error('Error init form. Check params!');
        }

        $(document).on('click', submit_selector, submit);
        $(document).on('click', open_selector, show);

        if (oConfigInit.init_callback) {
            oConfigInit.init_callback.call();
        }
    }
    function loadFillFields(oData)
    {
        for(key in oData)
        {
            if ("gender" == key)
            {
                $(selector_wrap).find('[name*="' + key + '"]').filter("[value='" + oData[key] + "']").trigger("click");
                return;
            }

            $(selector_wrap).find('[name*="' + key + '"]').val(oData[key]);
        }
    }
    return {
        init: init,
        loadFillFields: loadFillFields
    };
};

var ClassForm = function()
{
    var selector_wrap,
        submit_selector,
        submit_path;

    function loadSettings(oInitData)
    {
        if (!oInitData || !oInitData.selector_wrap || !oInitData.submit_selector || !oInitData.submit_path)
        {
            return false;
        }

        selector_wrap = oInitData.selector_wrap;
        submit_selector = oInitData.submit_selector;
        submit_path = oInitData.submit_path;

        return true;
    }
    function resetForm()
    {
        $(selector_wrap).find('.has_error').removeClass('has_error').end().find('.error_message').remove();
    }
    function successCallback(reply)
    {
        var jThis, k;

        resetForm();

        if (reply.toaster) {
            toaster({error: !reply.success, text: reply.toaster});
        }

        if(reply.success) {
            window.location.reload();
        } else {
            for(k in reply.error) {

                if ('image_id' == k) {
                    continue;
                }

                jThis = $(selector_wrap).find('[name*="' + k + '"]');
                jThis.closest('.wrap_field').addClass('has_error')
                    .append('<div class="error_message">' + reply.error[k] + '<br></div>');
            }
        }
    }
    function submit(e)
    {
        var oData = {}, oResult = {}, jSelects, jSelect, jThis, jSelect2Wrap, jSkillItem;

        $(selector_wrap).find("input[name], textarea").each(function() {
            jThis = $(this);

            if ("radio" == jThis.prop("type")) {
                if (jThis.prop("checked")) {
                    oData[jThis.prop("name")] = jThis.val();
                }
                return;
            }

            oData[jThis.prop("name")] = jThis.val();
        });

        /**
         * Сбор выбранных скиллов и грейды к ним
         * @type {*}
         */
        jSelects = $(selector_wrap).find("select");
        if (jSelects.length) {
            jSelects.each(function()
            {
                jSelect = $(this);

                jSelect2Wrap = jSelect.closest(".wrap_field").find(".select2");
                if (jSelect2Wrap.length) {
                    jSelect2Wrap.find("[data-id]").each(function ()
                    {
                        jSkillItem = $(this);
                        oResult[jSkillItem.data("id")] = jSkillItem.find('input[name="score"]').val() || 0;
                    });
                    oData[jSelect.prop("name").slice(0,-2)] = oResult;
                } else {
                    oData[jSelect.prop("name")] = jSelect.val();
                }
            });
        }

        $.ajax({
            url: submit_path,
            type: 'post',
            dataType: 'json',
            data: oData,
            success: successCallback
        });

    }
    function init(oConfigInit)
    {
        if (!loadSettings(oConfigInit)) {
            throw new Error('Error init form. Check params!');
        }

        $(document).on('click', submit_selector, submit);

        if (oConfigInit.init_callback) {
            oConfigInit.init_callback.call();
        }
    }
    return {
        init: init
    };
};

var DropzonePersonal = {
    init: function()
    {
        var jElement = $("#lk_photo-drop-image");
        if(jElement.length && !jElement.hasClass("dz-started")){
            jElement.dropzone({
                url: '/profile/image',
                paramName: 'User[avatar_image]',
                maxFilesize: 0.5,
                maxFiles: 1,
                acceptedFiles: "image/png,.jpg,.gif",
                uploadMultiple: false,
                thumbnailWidth: 300,
                thumbnailHeight: 150,
                dictFileTooBig: "Размер файла слишком большой",
                dictMaxFilesExceeded: 'Возможно загрузить не более 1 файла',
                dictInvalidFileType: 'Неподдерживаемый тип файла',
                headers: {
                    'x-csrf-token': $('meta[name="csrf-token"]').attr('content')
                },
                previewsContainer: ".lk_photo-drop-preview",
                previewTemplate: "<div class='change-form-avatar'><img data-dz-thumbnail /><span class='image_load_error'><span data-dz-errormessage=''></span></span></div>",
                clickable: ".lk_photo-drop-preview",
                init: function ()
                {
                    var _this = this;
                    this.on("success", function (file, responseText, e) {
                        if (responseText.error) {
                            for(var k in responseText.error) {
                                for(var i=0; i < responseText.error[k].length; i++ ) {
                                    $('.image_load_error').append(responseText.error[k][i] + '<br>');
                                }
                            }
                            $('.dz-image-preview img').css("display","none");
                            $('.image_load_error').addClass('active');
                        }

                        if(responseText.success) {
                            setTimeout(function(){
                                $("#lk_photo-drop-image").find('[name="User[avatar]"]').val(responseText.new_src).end()
                                    .find('[data-dz-thumbnail]').prop("src", responseText.new_src);
                            }, 0);
                        }
                    });

                    this.on("addedfile", function (file) {
                        jElement.find('.change-form-avatar').eq(0).remove();
                    });

                    this.on("error", function (file) {
                        $('.image_load_error').addClass('active');
                    });

                    this.on("maxfilesexceeded", function(file) {
                        $('.image_load_error').removeClass('active');
                        _this.files = [];
                        _this.addFile(file);
                    });
                }
            });
        }
    }
};

var oAllPage = {
    hInitToastr: function()
    {
        if (typeof(toastr) !== "undefined") {
            toastr.options = {
                "closeButton": false,
                "debug": false,
                "newestOnTop": true,
                "progressBar": false,
                "positionClass": "toast-top-right",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };

            window.toaster = function (data) {
                var type = data.success ? 'success' : (data.error ? 'error' : 'info');
                if (data.html) {
                    toastr[type](data.html);
                    return;
                }
                var text =
                    '<div class="toast-custom-text"><div class="toast-title">' + (data.title ? data.title : '') + '<div class="toast-close"></div></div></div>'+
                    '<div class="toast-text">' + (data.text ? data.text : '') + '</div>';
                toastr[type](text);
            };
        }
    },
    hFixSelect2: function()
    {
        var oSkillGrade = {};
        function afterSelect()
        {
            $('.skill_grade').raty({
                cancel  : true,
                starOff: '/images/star-off.png',
                starOn: '/images/star-on.png',
                cancelOff: '/images/cancel-off.png',
                cancelOn: '/images/cancel-on.png',
                click: function(grade, event)
                {
                    var jThis = $(this).closest(".skill_title");

                    jThis.data("skill_grade", grade);
                    oSkillGrade[jThis.data("id")] = grade;
                },
                score: function()
                {
                    var jThis = $(this).closest(".skill_title");
                    return oSkillGrade[jThis.data("id")] || jThis.data("skill_grade");
                }
            });

            $(".skill_title").off('click').on("click", function() {
                return false;
            });
        }

        var jSelect = $('select[name*="skill_ids"]');
        if (!jSelect.length) {
            return;
        }

        jSelect.on('select2:select',afterSelect);

        jSelect.on('select2:unselect', afterSelect);

        afterSelect();
    },
    hFixClickRadio: function(e)
    {
        var jThis = $(e.currentTarget).find("input[type='radio']");

        $("[name='" + jThis.prop("name") + "']").prop("checked", "0");
        jThis.prop("checked", "1");
    },
    hInit: function ()
    {
        oAllPage.hInitToastr();
        setTimeout(function() { oAllPage.hFixSelect2(); }, 0);

        $(document).on("click", ".radio_items", oAllPage.hFixClickRadio);
    }
};

$(function() {

    oAllPage.hInit();

    var LoginPopup = new ClassPopup();
    LoginPopup.init({
        selector_wrap: ".modal_signin_wrap",
        submit_selector: ".js_signin_submit",
        open_selector: ".js_signin_open",
        submit_path: "/site/signin"
    });

    var RegisterPopup = new ClassPopup();
    RegisterPopup.init({
        selector_wrap: ".modal_signup_wrap",
        submit_selector: ".js_signup_submit",
        open_selector: ".js_signup_open",
        submit_path: "/site/signup",
        init_callback: function() {
            if ('signup' == window.location.hash.slice(1))
            {
                var jWrap = $(".modal_signup_wrap"), key;

                jWrap.modal("show");
                window.location.hash = "";

                if ("undefined" !== typeof(signupSocialData) && signupSocialData) {
                    RegisterPopup.loadFillFields(signupSocialData);
                }
            }
        }
    });

    var ResumeForm = new ClassForm();
    ResumeForm.init({
        selector_wrap: ".resume",
        submit_selector: ".js_resume_create",
        submit_path: "/resume/create"
    });

    var ProfileForm = new ClassForm();
    ProfileForm.init({
        selector_wrap: ".profile",
        submit_selector: ".js_profile_edit",
        submit_path: "/profile/edit"
    });

    DropzonePersonal.init();

});