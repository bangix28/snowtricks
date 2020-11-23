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
