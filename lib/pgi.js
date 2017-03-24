/* post Gitlab issues - v.0.1.1 - licence MIT - http://sycom.gitlab.io/post-Gitlab-issues/ */
(function($) {
    $(function() {
        /* CREATING STYLES */
        $("head").append("<style id='pgiStyle'></style>");
        // getting colors !todo
        // creating styles
        var pgiStyle = "#pGi {display:;position:absolute;top:0;left:0;z-index:424242;width:100%;height:100%;background:rgba(255,255,255,0.7);}";
        pgiStyle += "#pGi>div {width:98%;max-width:450px;margin:30px auto;padding:1rem 1rem 2rem 2rem;border-radius:4px;box-shadow:15px 15px 10px rgba(200,200,200,.9)}";
        pgiStyle += "#pGi .pgiClose {float:right}";
        pgiStyle += ".pgiClose {cursor:pointer;cursor:hand;}";
        pgiStyle += ".pgiClose a {text-decoration:none;border:none}";
        pgiStyle += "#pGi input[type='text'],#pGi input[type='email'],#pGi input[type='password'],#pGi input[type='url'],#pGi select,#pGi textarea {margin:.5em 0;box-sizing:border-box;position:relative;-moz-appearance: none;-webkit-appearance: none;appearance:none;display:inline-block;border:0;outline:0;width: 100%;border-radius: 0.35em;padding: 0.5em .5em 0.5em .75em;-moz-transition: all 0.35s ease-in-out;-webkit-transition: all 0.35s ease-in-out;-o-transition: all 0.35s ease-in-out;-ms-transition: all 0.35s ease-in-out;transition: all 0.35s ease-in-out;}";
        pgiStyle += "#pGi textarea {margin:.5em 0;width:100%;border-radius:5px;border:0;padding:.5em 1em;box-sizing:border-box}";
        pgiStyle += "#pGi input[type='button'],#pGi input[type='submit'],#pGi input[type='reset'] {position: relative;display:inline-block;border-radius: 0.25em;text-decoration: none;margin:1.25em 0 0 0;padding: 0.75em 2.5em 0.75em 2.5em;border: 0;cursor:pointer;-moz-transition: all 0.35s ease-in-out;-webkit-transition: all 0.35s ease-in-out;-o-transition: all 0.35s ease-in-out;-ms-transition: all 0.35s ease-in-out;transition: all 0.35s ease-in-out;}";
        pgiStyle += ".pgiSubmit {text-align:center}";
        pgiStyle += ".pgiHidden {display:none !important}";
        // dynamic colors
        pgiStyle += "#pGi>div {background:#555}";
        pgiStyle += "#pGi>div p {color:#ddd}";
        pgiStyle += "#pGi a:hover {color:#ddd}";
        pgiStyle += "#pGi .pgiClose:hover {color:" + $(".fa-inverse").css("color") + "}";
        pgiStyle += "#pGi .pgiClose:hover a {color:" + $(".fa").css("color") + "}";
        // some responsiveness
        pgiStyle += "@media (min-width: 750px) {#pGi>div {width:64%;max-width:750px}}"
        // applying styles
        $("#pgiStyle").text(pgiStyle);

        /* CREATING DIALOGS */
        /* create the dialog div and the sender iframe */
        var pgi_baseHtml = "<div id='pGi' style='display:none'><div><span class='fa-stack fa-lg pgiClose'><i class='fa fa-circle fa-stack-2x'></i><a href='#' class='fa fa-close fa-stack-1x fa-inverse'></a></span><p id='pgiIntro'>"+pGi.intro+"</p><form id='pgiForm'></form></div></div><div class='pgiHidden'><iframe id='pgiSender' name='pgiSender' width='0' height='0'></iframe></div>";
        $("body").append(pgi_baseHtml);
        /* add form fields */
        for (var i in pGi.form) {
            pgi_addForm(pGi.form[i]);
        }

        /* function for creating form field */
        function pgi_addForm(obj) {
            var pgi_inputHtml = "";
            switch (obj.type) {
                case "text":
                case "url":
                case "email":
                case "password":
                    pgi_inputHtml += "<input name='" + obj.name + "' id='" + obj.name + "' value='" + obj.def + "' type='" + obj.type + "'></input>";
                    break;
                case "textarea":
                    pgi_inputHtml += "<textarea name='" + obj.name + "' id='" + obj.name + "' default='" + obj.def + "' type='textarea'>" + obj.def + "</textarea>";
                    break;
                default:
            }
            $("#pgiForm").append(pgi_inputHtml);
            if (obj.req === true) $("#" + obj.name).attr("required", "true");
            if (obj.hid === true) $("#" + obj.name).addClass("pgiHidden");
        }

        /* creating submit button and thanks message */
        var pgi_buttonHtml = "<div class='pgiSubmit'><input type='submit' name='pgiSubmit' id='pgiSubmit'></input></div>";
        $("#pgiForm").append(pgi_buttonHtml);
        var pgi_thanksHtml = "<div id='pgiThanks'><p>"+pGi.thanks+"</p></div>";
        $("#pGi>div").append(pgi_thanksHtml);

        /* CREATING FUNCTIONS */
        // make comments form appear and disappear
        function openPgi() {
            $("#pgiThanks").hide();
            $("#pGi").fadeIn(".75s");
            $("#pGi>div").fadeIn(".95s");
            $("#pgiIntro").fadeIn("1.05s");
            $("#pgiForm").fadeIn("slow");
        }
        function closePgi() {
            $("#pGi>div").fadeOut(".5s");
            $("#pGi").fadeOut(".75s");
        }
        // make waiting message appearance
        function waitPgi() {
           $("#pgiIntro").fadeOut();
           $("#pgiForm").fadeOut("slow");
           $("#pgiThanks").fadeIn("slow");
        }
        // add functions to links and buttons
        $(".pgiButton").click(openPgi);
        $(".pgiClose").click(closePgi);
        $(".pgiSubmit").click(waitPgi);

        // form submission
        var $form= $("#pgiForm");
        // targetting sender iframe
        $form.submit(function(evt) {
            // define target url
            var pgiFormUrl = "https://" + pGi.gitlab + "/api/v3/projects/" + pGi.usgroup + "%2F" + pGi.project + "/issues?private_token=" + pGi.token;
            // get data in form and group by destination fields
            $form.serialize();
            var formData = new Object();
            for (var i in pGi.form) {
                if (formData[pGi.form[i].dest]==undefined) formData[pGi.form[i].dest] = $("#" + pGi.form[i].name).val();
                else formData[pGi.form[i].dest] += " - " + $("#" + pGi.form[i].name).val();
            }
            console.log("pGi > "+ pgiFormUrl);
            console.log(formData);
            $form.attr('target','pgiSender');
            $.post(pgiFormUrl, formData, function(data) {
               console.log("pGi > Response id: " + data.iid);
                var newThanks=pGi.thanks+"<br/>ref : <a href='https://" + pGi.gitlab + "/" + pGi.usgroup + "/" + pGi.project + "/issues/" + data.iid +"' target='_blank'>https://" + pGi.gitlab + "/" + pGi.usgroup + "/" + pGi.project + "/issues/" + data.iid +"</a>";
                // Thanks and feedback
                $("#pgiThanks p").html(newThanks);
               // display thanks
               if(pGi.autoclose!==false) window.setTimeout(closePgi,pGi.autoclose);
               // clean form : reset to default value
            });
        });
    })
})(jQuery);
