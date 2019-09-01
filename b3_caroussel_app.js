angular.module("b3_caroussel",[]).controller("mainSlider",function($http){
    var self=this;
    self.home=document.getElementById("homeAddress").value+"/wp-json";
    self.b3Namespace="/b3caroussel/v1";
    self.loadingSlides=true;    
    self.items=[];
    
    $http.get(self.home+self.b3Namespace+"/slideshow").then(function(response){
        self.items=response.data;
        //alert(JSON.stringify(response.data));
        self.loadingSlides=false;
    },
    function(errorResponse){
        console.error("Something bad happened");
    });
});