// console.log(SliderVar);
var  sliderHtml = jQuery.parseJSON(SliderVar.htmlbigDescription);
var sliderdata = jQuery.parseJSON(SliderVar.sliderData);
var ajaxurl = SliderVar.ajax;
var indexRating = -1;
console.log(sliderHtml,sliderdata);


jQuery(window).load(function() {
});

jQuery(document).ready(function() {

});

var current_star = [];
var Slen = sliderdata.length;
var index = 0,prev = 0;
ratingFunction();
setInterval(function (){
  index = (index+1)%Slen;
  sliderHtml[prev] = jQuery("#bigImgContent").html();
  jQuery("#bigImgContent").html(sliderHtml[index]);
  jQuery("#showrightimgs .smallimg").first().remove();
  let htm = '<span class = "smallimg" ><img src = "'+ sliderdata[prev]["gmeta_data"]["content_img"] +'" ></span>';
  jQuery("#showrightimgs").append(htm);
  prev = index;
  indexRating = index;		 
  //sliderdata[indexRating]["gmeta_data"]
  console.log(index);
  jQuery(".rate_it_now .star-rating .star").off();
  jQuery(".rate_it_now .star-rating").off();
  ratingFunction();
},15000);



function ratingFunction(){

  jQuery(".rate_it_now .star-rating .star").click(function(){
   let star = jQuery(this);
   star.parent().find(".star").removeClass('star-empty').removeClass('star-full');
   star.addClass('star-full');
   star.prevAll().addClass('star-full');
   star.nextAll().addClass('star-empty');
   storeCurrentRating()
   let data = {};
   rates = star.parent().find(".star.star-full").length;
   data.data = rates;
   data.id =  sliderdata[indexRating]["gmeta_data"]["id"];
   console.log(data);
   jQueryAjax(data);

 });

  storeCurrentRating();

  jQuery(".rate_it_now .star-rating .star").mouseenter(changeratingClass); 
  jQuery(".rate_it_now .star-rating").mouseleave(resetratingClass);
  function resetratingClass() {
    var stars = jQuery(this).find(".star");
    console.log("out");
    stars.each(function(i, elem) {
      jQuery(elem).removeClass('star-full').removeClass('star-empty')
      console.log(current_star,"reset");
      jQuery(elem).addClass((current_star[i] ? 'star-full' : 'star-empty'));
    });
  }
  function changeratingClass() {

    var star  =  jQuery(this);
    console.log(star,"inn");
    star.parent().find(".star").removeClass('star-empty').removeClass('star-full');
    star.addClass('star-full');
    star.prevAll().addClass('star-full');
    star.nextAll().addClass('star-empty');
  }
  function storeCurrentRating(){
   current_star = [];
   jQuery(".rate_it_now .star-rating .star").each(function(i, elem) {
     console.log(elem);
     current_star.push(jQuery(elem).hasClass('star-full'));
   });
   console.log(current_star);
 }

 function jQueryAjax(data){
  jQuery.post(ajaxurl, {
    'action': 'addrating',
    'data': data,
  }, function(val) {
    console.log(val);
  });
}

}