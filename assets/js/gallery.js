//Editing multiple images
$("#gallery-body").on("click", "#delete-btn", function (e) {
    e.preventDefault();
    var formData = new FormData($("#gallery").get(0));

    let action = $(this).data("action");
    formData.append("action", action);
    $.ajax({ //start ajax post
        type: "POST",
        url: "scripts/gallery.scriptnew.php",
        data: formData,
        contentType: false,
        processData: false,
        success: function (data, responseText) {
            ///need script to catch errors
            $("#gallery-body").load("scripts/gallery.scriptnew.php?action=load_gallery");
        }
    });

});
$("#gallery-body").on("change","#placement", function (e) {
    e.preventDefault();
    var formData = new FormData($("#gallery").get(0));
    
    let action = $(this).data("action");
    formData.append("action", action);
    $.ajax({ //start ajax post
        type: "POST",
        url: "scripts/gallery.scriptnew.php",
        data: formData,
        contentType: false,
        processData: false,
        success: function (data, responseText) {
            ///need script to catch errors
            $("#gallery-body").load("scripts/gallery.scriptnew.php?action=load_gallery");
        }
    });

});

//check all check boxes
$("#gallery-body").on("change" ,"#check_all", function () {
    $(".gallery-select").not(this).prop('checked', this.checked)
    $(".gallery-select").each(function () {
        if ($(this).is(":checked")) {
            $(this).parent().siblings($("tr")).addClass("gallery-selected")
        } else (
            $(this).siblings($("tr")).removeClass("gallery-selected")
        )

    })
})

$("#gallery-body").on("change",".gallery-select",  function () {
    if ($(this).is(":checked")) {
        $(this).parent().siblings().addClass("gallery-selected")
    } else (
        $(this).parent().siblings().removeClass("gallery-selected")
    )

})

$("#gallery-body").on("focusout", ".caption", function(){
    let image_id = $(this).data("imgid");
    let caption = $(this).text();
    let action = $(this).data("action");
    let formData = new FormData();
    formData.append("action", action);
    formData.append("image_id", image_id);
    formData.append("caption", caption);
    $.ajax({ //start ajax post
        type: "POST",
        url: "scripts/gallery.scriptnew.php",
        data: formData,
        contentType: false,
        processData: false,

    });
})

$("#gallery-body").on("click", "#upload-btn", function(e){
    e.preventDefault();
    var formData = new FormData($("#gallery").get(0));
    
    let action = $(this).data("action");
    formData.append("action", action);
    $.ajax({ //start ajax post
        type: "POST",
        url: "scripts/gallery-multiple.php",
        data: formData,
        contentType: false,
        processData: false,
        beforeSend: function() { //animate button
            $("#loading-icon").show(400);
        },
        complete: function() {
            $("#loading-icon").hide(400);
        },
        success: function (data, responseText) {
            ///need script to catch errors
            if(responseText === "success"){
                $("#gallery-body").load("scripts/gallery.scriptnew.php?action=load_gallery");
            }
        }
    });
})
$("#gallery-body").on("click", "#upload-show", function(){
    $("#upload-card").slideToggle(400);
})
$("#gallery-body").on("click", "#close-upload", function(){
    $("#upload-card").slideUp(400);
})