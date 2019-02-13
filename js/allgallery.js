var ajaxurl=galleryVar.ajax;
console.log(ajaxurl,"aasadsasd");
var rated=0;
var current_star=[];
var flagRating=0;
jQuery(window).load(function() {
});

jQuery(document).ready(function() {

});

// jQuery("#AwareArticles").click(9,CallMedia) ;
// function CallMedia(event){    
//     let Data={};
//     // console.log("asd");
//     Data.catid=event.data;
//     jQueryAjax(Data);
//  }   
function jQueryAjax(data){
    jQuery.post(ajaxurl, {
                'action': 'filterMedia',
                'data': data
            }, function(val) {
              jQuery("#news_listing").html(val);
              console.log(val);
          });
}

jQuery(".rate_it_now").on("hover",function() {
  current_star=[];
  var curr=jQuery(this).find(".star-rating");
  curr_starElement=curr.find(".star");
  if(!flagRating){
  curr_starElement.each(function(i, elem) {
      console.log(elem);
      current_star.push(jQuery(elem).hasClass('star-full'));
  });
   console.log(current_star);
   flagRating=1;
  }
  curr_starElement.mouseenter(changeratingClass); 
  curr_starElement.mouseleave(resetratingClass);
})
function resetratingClass() {
  var stars=jQuery(this).parent();
  console.log("out");
  stars.each(function(i, elem) {
    jQuery(elem).removeClass('star-full')
    jQuery(elem).removeClass('star-empty')
    console.log(current_star,"reset");
    jQuery(elem).addClass((current_star[i] ? 'star-full' : 'star-empty'));
  });
  flagRating=0;
}
function changeratingClass() {

  var star = jQuery(this);
  console.log(star,"inn");
  jQuery('.star').removeClass('star-empty').removeClass('star-full');
  star.addClass('star-full');
  let myparent =star.parent();
  myparent.prevAll().find('.star').addClass('star-full');
  myparent.nextAll().find('.star').addClass('star-empty');
}