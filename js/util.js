function makeStarLayout(value) {
    var filledStars = Math.round(value);
    var emptyStars = 5 - filledStars
    var string1 = "";
    var string2 = "";
    for (var i = 0; i < filledStars; i++) {
        string1 += "&#9733";
    }
    for (var i = 0; i < emptyStars; i++) {
        string2 += "&#9733";
    }
    return '<span class="filledrating">' + string1 + '</span><span class="emptyrating">' + string2 + '</span>';
}

function makeAllStarLayouts() {
    var elements = document.getElementsByClassName("ratingstars");
    for (var i = 0; i < elements.length; i++) {
        elements[i].innerHTML = makeStarLayout(parseFloat(elements[i].innerHTML));
    }
}