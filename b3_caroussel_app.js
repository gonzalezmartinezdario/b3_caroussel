var extScope;
var idSlide;
angular.module("b3_caroussel",[]).controller("mainSlider",function($scope, $http){
    var self=this;
    extScope=$scope;
    self.frame;
    self.restAddress=document.getElementById("homeAddress").value+"/wp-json";
    self.home=document.getElementById("homeAddress").value;
    self.b3Namespace="/b3caroussel/v1";
    self.loadingSlides=true;    
    self.items=[];   
    self.retrievedSlides=[];    
    
    //CRUD
    self.retrieveSlides= function(){
   self.loadingSlides=true; $http.get(self.restAddress+self.b3Namespace+"/slideshow").then(function(response){
       if(response.data){
           self.retrievedSlides=response.data;                  
        }else{
            self.retrievedSlides=[];                    
        }       
        self.items= JSON.parse(JSON.stringify(self.retrievedSlides));
        //alert(JSON.stringify(response.data));
        self.loadingSlides=false;
        },function(errorResponse){
            console.error("Something bad happened");
        });    
    }
    self.addSlide= function(){        
        self.items.push({
            id:0,
            title: "Nueva",
            description: "Nueva diapositiva",
            url: self.home,
            slide: self.home+"/wp-content/plugins/caroussel4ursite/slide.png"
        });
    }
    self.removeSlide=function(slideIndex){
        self.items.splice(slideIndex,1);
    }
    self.getAddedSlides= function(slideArray){        
        var newSlides=[];
        
        for(var i=0; i<slideArray.length;i++){
            if(slideArray[i].id==0) newSlides.push(slideArray[i]);
        }
        
        return newSlides;
    }
    self.containsID=function(id, managedArray){
        for(var i=0; i<managedArray.length; i++){
            //Esta en los slides
            if(managedArray[i].id==id) return true;
        }
        //No esta en los slides
        return false;
    }
    self.getIndex=function(id, managedArray){
        for(var i=0; i<managedArray.length; i++){
            //Esta en los slides
            if(managedArray[i].id==id) return i;
        }        
    }
    self.getRemovedSlides= function(originalArray, managedArray){
        var removedSlides=[];
        for(var i=0;i<originalArray.length; i++){
            if(!self.containsID(originalArray[i].id,managedArray))
                removedSlides.push(originalArray[i]);
        }
        return removedSlides;
    }    
    self.getUpdatedSlides= function(originalArray, managedArray){
        var updatedSlides=[];
        var slide=0;
        for(var i=0;i<originalArray.length;i++){                        
            if(self.containsID(originalArray[i].id, managedArray)){             
                slide=self.getIndex(originalArray[i].id, managedArray);
                if((originalArray[i].title!=managedArray[slide].title) ||
                  (originalArray[i].description             !=managedArray[slide].description) ||
                   (originalArray[i].url!=managedArray[slide].url) ||
                   (originalArray[i].slide!=managedArray[slide].slide)
                  )
                    updatedSlides.push(managedArray[slide]);
            }
        }
        return updatedSlides;
    }
    self.saveChanges= function(){
        /*alert("Arreglo Original:"+ JSON.stringify(self.retrievedSlides) +
              "\n"+
              "Arreglo Actual: "+ angular.toJson(self.items));
        alert("Nuevos: "+ JSON.stringify(self.getAddedSlides(self.items)));
        alert("Removed: "+ JSON.stringify(
            self.getRemovedSlides(self.retrievedSlides,self.items)
        ));        
        alert("Updated: "+ JSON.stringify(
            self.getUpdatedSlides(self.retrievedSlides,self.items)
        ));*/
        
        self.loadingSlides=true;    
        var added=self.getAddedSlides(self.items),
            removed=self.getRemovedSlides(self.retrievedSlides,self.items),
            updated=self.getUpdatedSlides(self.retrievedSlides,self.items);
        
        $http.post(self.restAddress+self.b3Namespace+"/slideshow", {request: JSON.stringify(added)}).then(function(response){                   
                return $http({
                        method: 'DELETE',
                        url: self.restAddress+self.b3Namespace+"/slideshow",
                        data: {
                            request: JSON.stringify(removed)
                        },
                        headers: {
                        'Content-type': 'application/json;charset=utf-8'
                        }
                        });    
        }).then(function(){
            return $http.put(self.restAddress+self.b3Namespace+"/slideshow",{request: JSON.stringify(updated)});
        }).then(function(msg){            
            self.retrieveSlides();
        }, function(rejection){            
            alert(rejection);
        });        
    }   
    self.mediaLibrary= function(id){
        idSlide=id;
        //If the fame exists: reopen it        
        if(self.frame){
            self.frame.open();
            return;
        }
        
        //If it doesn't exist: Create a new one
        self.frame= wp.media({
            title: "Seleccione o suba la imagen para la diapositiva",
            button: {
                text:"Aceptar"
            },
            multiple: false
        });
        
        //When an img is selected
        self.frame.on('select', function(){
            extScope.$apply(function(){                
                //Get de img details
                var attachment=self.frame.state().get('selection').first().toJSON();                    
                self.items[idSlide].slide=attachment.sizes.thumbnail.url;       
            });
            
        });
        self.frame.open();
    }
    
    //Ejecucion
    self.retrieveSlides();
    
    
});