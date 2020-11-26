$(function () {
    $("div.trick").slice(6, $("div.trick").length).hide();
    $("#loadLessTrick").hide("slow");

    $("#loadMoreTrick").on("click", function (e) {
        e.preventDefault();
        $("div.trick:hidden").slice(0, 6).slideDown();
        if ($("div.trick:hidden").length === 0) {
            $("#loadMoreTrick").hide("slow");
            $("#loadLessTrick").show("slow");
        }
    });
    $("#loadLessTrick").on("click", function (e) {
        e.preventDefault();
        $("div.trick").slice(6, $("div.trick").length).hide();
        $("#loadLessTrick").hide("slow");
        $("#loadMoreTrick").show("slow");

    });

});
$(function () {
    $(function () {
        $("#loadMedia").on("click", function (e) {
            e.preventDefault();
            $(".media").removeClass("d-none");
            $("#loadMedia").addClass("d-none");
            $("#hiddenMedia").removeClass("d-none");
        });
        $("#hiddenMedia").on("click", function (e) {
            e.preventDefault();
            $(".media").addClass("d-none");
            $("#loadMedia").removeClass("d-none");
            $("#hiddenMedia").addClass("d-none");
        });
    });
});


