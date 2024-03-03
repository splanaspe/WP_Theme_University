import $ from 'jquery';

class Search{
    // 1. Describe and create/initiate our object
    constructor() {
        console.log("inicializado Search.js");
        this.addSearchHTML();
        this.openButton = $(".js-search-trigger");
        this.closeButton = $(".search-overlay__close");
        this.searchOverlay = $(".search-overlay");
        this.searchField = $("#search-term");
        this.resultsDiv = $("#search-overlay__results");
        this.events(); // Ejecuta la funcion events
        this.isOverlayopen = false;
        this.typingTimer;
        this.previousValue;
        this.isSpinnerVisible = false;
        
    }

    // 2. Events 
    events() {
        this.openButton.on('click', this.openOverlay.bind(this));
        this.closeButton.on('click',this.closeOverlay.bind(this));
        $(document).on('keydown',this.keyPressDispatcher.bind(this));
        this.searchField.on('keyup', this.typingLogic.bind(this) );
    }

    // 3. Methods (function, action)
    typingLogic(){
        if(this.searchField.val() != this.previousValue){
            clearTimeout(this.typingTimer);

            if(this.searchField.val()){
                if(!this.isSpinnerVisible){
                    this.resultsDiv.html('<div class="spinner-loader"> </div>'); // funcion que muestra un spinner de carga
                    this.isSpinnerVisible=true;  
                }
                this.typingTimer = setTimeout(this.getResults.bind(this), 750);
            } else {
                this.resultsDiv.html('');
                this.isSpinnerVisible = false;
            }
        }W
        this.previousValue = this.searchField.val(); // Da el valor del input en la variable
    }
    
    getResults(){
        $.getJSON(universityData.root_url + "/wp-json/university/v1/search?term=" + this.searchField.val(), results => {
            this.resultsDiv.html(`
                <div class="row">
                <div class="one-third">
                    <h2 class="search-overlay__section-title">General Information</h2>
                    ${results.generalInfo.length ? '<ul class="link-list min-list">' : "<p>No general information matches that search.</p>"}
                    ${results.generalInfo.map(item => `<li><a href="${item.permalink}">${item.title}</a> ${item.postType == "post" ? `by ${item.authorName}` : ""}</li>`).join("")}
                    ${results.generalInfo.length ? "</ul>" : ""}
                </div>
                <div class="one-third">
                    <h2 class="search-overlay__section-title">Programs</h2>
                    ${results.programs.length ? '<ul class="link-list min-list">' : `<p>No programs match that search. <a href="${universityData.root_url}/programs">View all programs</a></p>`}
                    ${results.programs.map(item => `<li><a href="${item.permalink}">${item.title}</a></li>`).join("")}
                    ${results.programs.length ? "</ul>" : ""}

                    <h2 class="search-overlay__section-title">Professors</h2>
                    ${results.professors.length ? '<ul class="link-list min-list">' : `<p>No professors match that search.</p>`}
                    ${results.professors.map(item => `
                        <li class="professor-card__list-item">
                        <a class="professor-card" href="${item.permalink}"> 
                            <img class="professor-card__image" src="${item.image}"> 
                            <span class="professor-card__name"> ${item.title} </span>
                        </a>
                        </li>
                        `).join("")}
                    ${results.professors.length ? "</ul>" : ""}
                </div>
                <div class="one-third">
                    <h2 class="search-overlay__section-title">Campuses</h2>
                    ${results.campuses.length ? '<ul class="link-list min-list">' : `<p>No campuses match that search. <a href="${universityData.root_url}/campuses">View all campuses</a></p>`}
                    ${results.campuses.map(item => `<li><a href="${item.permalink}">${item.title}</a></li>`).join("")}
                    ${results.campuses.length ? "</ul>" : ""}

                    <h2 class="search-overlay__section-title">Events</h2>
                    ${results.events.length ? '' : `<p>No events match that search. <a href="${universityData.root_url}/events">View all events</a></p>`}
                    ${results.events.map(item => `
                    <div class="event-summary">
                        <a class="event-summary__date t-center" href="${item.permalink}">
                            <span class="event-summary__month"> 
                                ${item.month}
                            </span>
                            <span class="event-summary__day">
                                ${item.day}
                            </span>
                        </a>
                        <div class="event-summary__content">
                            <h5 class="event-summary__title headline headline--tiny">
                                <a href="${item.permalink}"> ${item.title} </a>
                            </h5>
                            <p> ${item.description} </p>
                        </div>
                    </div>
                    `).join("")}
                    
                </div>
            </div>
        `)
        this.isSpinnerVisible = false
    })

        /*
        $.when(
            $.getJSON( universityData.root_url+'//wp-json/wp/v2/posts?search='+ this.searchField.val() ), 
            $.getJSON(universityData.root_url+'//wp-json/wp/v2/pages?search='+ this.searchField.val() ) 
            ).then( (posts,pages) => {
            var combinedResults = posts[0].concat(pages[0]);
                this.resultsDiv.html(`
                <h2 class="search-overlay__section-title"> General Information </h2>

                ${combinedResults.length ? 
                    `<ul class="link-list min-list">`
                    :
                    `<p> No general information matches that search. </p>`
                }
                    ${combinedResults.map( item =>  
                        `<li><a href="${item.link}"> ${item.title.rendered} </a> ${ item.type=='post' ? `by ${item.authorName}` : ``} </li>`).join('') }
                ${combinedResults.length ? 
                    `</ul>`
                        :
                    ``
                }
            `);
            this.isSpinnerVisible = false;
        }, () =>{
            this.resultsDiv.html('<p> Unexpected error, try it again </p>');
        });
        */
    }

    openOverlay() {
        this.searchOverlay.addClass("search-overlay--active");
        $("body").addClass("body-no-scroll");
        this.searchField.val('');
        setTimeout(() =>this.searchField.focus(), 301); 
        this.isOverlayopen = true;
        return false; //Aixo impedeix que canvi a la pag serarch si JS esta activat
    }

    keyPressDispatcher(e) {
        // e.keyCode ens retorna el nº de tecla que hem tocat, per veure-ho
        //podem mostrar alert(e.keyCode) aixi podem programar millor

        if(e.keyCode == 220 && !this.isOverlayopen && $("input, textarea").is(':focus')){
            this.openOverlay();
        }

        if(e.keyCode == 27 && this.isOverlayopen){
            this.closeOverlay();
        }
    }

    closeOverlay() {
        this.searchOverlay.removeClass("search-overlay--active");
        $("body").removeClass("body-no-scroll");
        this.isOverlayopen = false;
    }


    addSearchHTML() {
        $("body").append(`
        <!-- Escribimos aqui el codigo del div live search-->
        <div class="search-overlay">
            <div class="search-overlay__top">
                <div class="container">
                    <i class="fa fa-search search-overlay__icon" aria-hidden="true"> </i>
                    <input type="text" class="search-term" placeholder="What are you looking for?" id="search-term">
                    <i class="fa fa-window-close search-overlay__close" aria-hidden="true"> </i>
                </div>
            </div>
        
            <div class="container">
                <div id="search-overlay__results"> 
                    
                </div>
            </div>
        
        </div>
        `);
    }
}


export default Search;