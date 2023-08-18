
if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
}
$("#delete-profile").click(function () {
    $(".modal").addClass("modal-active");
})
$("#upload_image").click(function() {
    $(".modal").addClass("modal-active");
})
$("#new_request").click(function() {
    $(".modal").addClass("modal-active");
})
//close modal when close button is clicked
$("#modal-btn-close").click(function() {
    $(".modal").removeClass("modal-active");

})
//close modal when close button is clicked
$("#delete-cancel").click(function() {
    $(".modal").removeClass("modal-active");

})

//close modal when close button is clicked
$("#cancel").click(function() {
    $(".modal").removeClass("modal-active");

})

//show dropdown menu when button clicked
$(".dropdown-btn").on("click", function(){
    $(this).siblings("ul").fadeToggle(400);
})

$("#new-pw-form").on("click", function () {
    $("#pw-form").slideToggle(400);
})

//settings forms
$("#reviews_api").on("submit", function (e) {
    e.preventDefault();
    let action = "reviews_api";
    var formData = new FormData($("#reviews_api").get(0));
    formData.append("action", action);
    $.ajax({ //start ajax post
        type: "POST",
        url: "scripts/functions.php",
        data: formData,
        contentType: false,
        processData: false,

        success: function (data, responseText) {
            const response = JSON.parse(data);
            if (response.response_code = 200) {
                console.log(response.response_code);
                $("#reviews_api .input-response__check").show(400);
                $("#reviews_api .input-response input").addClass("success");
                $("#response-card-text").html("Reviews API updated");
                $(".response-card-wrapper").fadeIn(400);
                $(".response-card-wrapper").delay(5000).fadeOut(400);
             }   
        }
    });
})