var toggleHash = new Array(20);

Effect.MoveAndReSize = Class.create();
Object.extend(Object.extend(Effect.MoveAndReSize.prototype, Effect.Base.prototype), {
    initialize: function(element) {

        this.element = $(element);        
        if(!this.element) throw(Effect._elementDoesNotExistError);

        // AVAILABLE OPTIONS (all is optional) 
        //{opacity: 0.2 ,move:['all:200:300','x:500','y:400','fixed:100:100'], resize: ['all:200:300','y:400','x:500','fixed:100:100'], toggleIndex:1...x}
        var options = Object.extend({move:[], resize: []}, arguments[1] || {});
        var goMove =( options.move.length > 0);
        var goResize = (options.resize.length > 0);
        var goToggle = (options.toggleIndex!=null);
        var goOpacity = (options.opacity!=null);
        if(options.duration==null)options.duration=1;

        var dimension = Element.getDimensions(this.element);
        var position = Position.positionedOffset(this.element);

        if (goToggle) 
        {
            originalElement = toggleHash[options.toggleIndex];

            // Prevent mutliclic on same object
            if (originalElement!=null)        
                if (originalElement.usedNow==true) return;
                    else originalElement.usedNow=true;

            this.goToggle(options,dimension,position);
        }

        options.addToSize = new Object();

        if (goResize)
            this.goResize(options,dimension);    

        if(goOpacity)
            this.goOpacity(options);

        if (goMove)    
      		this.goMove(options,position);

        if (goResize || goOpacity) 
            this.start(options);
    },

      initToggle: function(index){
        if (index!=null)
            toggleHash[index].usedNow=false;
    },

    goToggle: function(options,dimension,position){

        index = options.toggleIndex;
        originalElement = toggleHash[index];

        if (originalElement==null)
        {            
            toggleHash[index] = new Array(dimension,position,this.element.getStyle('opacity'),this.element.getStyle('z-index'));
        }
        else if (!toggleHash[index].isInitPosition)
        {
            options.resize = new Array('fixed:'  + originalElement[0].width +':'+originalElement[0].height);
            options.move   = new Array('fixed:'  + originalElement[1][0] +':'+originalElement[1][1]);
            options.opacity= originalElement[2];

            this.element.style.zIndex = originalElement[3];
            toggleHash[index].isInitPosition = true;
        }
        else toggleHash[index].isInitPosition = false;
    },  

    goResize: function(options,dimension) {

        this.originalSize = new Object();            

        options.resize.each( 
                function(szd) { 
                    tmp = szd.split(':');
                    if ((tmp[0]=='all') || (tmp[0]=='y')){
                        options.addToSize.changeY = true;
                        options.addToSize.y = parseInt(((tmp[0]=='y')?tmp[1]:tmp[2]));
                    }

                    if ((tmp[0]=='all') || (tmp[0]=='x')){
                        options.addToSize.changeX = true;
                        options.addToSize.x = parseInt(tmp[1]);
                    }

                    if (tmp[0]=='fixed')  {
                        options.addToSize.changeX = true;
                        options.addToSize.changeY = true;

                        options.addToSize.x = parseInt(tmp[1]) - dimension.width;                         
                        options.addToSize.y = parseInt(tmp[2]) - dimension.height;             
                    }
                } 
         );

        this.originalSize.x = dimension.width;
        this.originalSize.y = dimension.height;

    },  

    goMove: function(options,position) {
        var x = 0;
        var y = 0;
//        var duration = 0.3;
        options.move.each( 
                function(szd) { 
                    tmp = szd.split(':');

                    if ((tmp[0]=='all') || (tmp[0]=='y')){y = parseInt(((tmp[0]=='y')?tmp[1]:tmp[2]));}                
                    if ((tmp[0]=='all') || (tmp[0]=='x')){x = parseInt(tmp[1]);}

                    if (tmp[0]=='fixed')  {
                        x = parseInt(tmp[1]) - position[0];                     
                        y = parseInt(tmp[2]) - position[1]; 
                    }
                } 
         );

        new Effect.Move(this.element,{x:x,y:y,duration:options.duration, afterFinish: this.initToggle(options.toggleIndex)});
    },

    goOpacity: function(options){
        this.originalOpacity = parseFloat(this.element.getStyle('opacity'));
        options.changeOpacity = true;
        options.addTOpacity = parseFloat(options.opacity) - this.originalOpacity;
    },

    setOpacity: function(opacity) {
        this.element.style.opacity =''+opacity;
        this.element.style.filter = 'alpha(opacity='+(opacity*100)+')';
    },

    setup: function() {
        if(!this.element.visible()) { this.cancel(); return; }
    },

    update: function(position) {
        if(this.options.addToSize.changeX)
          this.element.setStyle({width: this.originalSize.x+(this.options.addToSize.x*position)+'px'});

        if(this.options.addToSize.changeY)   
          this.element.setStyle({height: this.originalSize.y+(this.options.addToSize.y*position)+'px'});

        if(this.options.changeOpacity)   
          this.setOpacity(this.originalOpacity+(this.options.addTOpacity*position));

    },

    finish: function(){
		if(this.options.goAfterFinish!=null){
			eval(this.options.goAfterFinish);
		}	
        if(this.options.addToSize.changeX)
          this.element.setStyle({width: this.originalSize.x+this.options.addToSize.x+'px'});

        if(this.options.addToSize.changeY)  
          this.element.setStyle({height: this.originalSize.y+this.options.addToSize.y+'px'});

        if (this.options.toggleIndex!=null) 
            toggleHash[this.options.toggleIndex].usedNow=false;

        if(this.options.changeOpacity)   
        {
          this.setOpacity(this.originalOpacity+this.options.addTOpacity);
          // Initialize z-index if layer is hidden (opacity = 0.0)                        

          if((this.originalOpacity+this.options.addTOpacity)==0.0)
            this.element.style.zIndex = 1;
        }

    }
});