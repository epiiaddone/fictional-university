import $ from 'jquery';

class Search {
    constructor() {
        this.addSearchHTML();
        this.openButton = $(".js-search-trigger");
        this.closeButton = $(".search-overlay__close");
        this.searchOverlay = $(".search-overlay");
        this.searchField = $('#search-term');
        this.resultsDiv = $('#search-overlay__results');
        this.typingTimer;
        this.previousValue;

        //quicker to use internal state rather than checking the dom with:
        //this.searchOverlay.hasClass("search-overlay--active")
        this.isOverlayOpen = false;

        this.isSpinnerVisible = false;

        this.events();
    }

    events() {
        this.openButton.on('click', this.openOverlay.bind(this));
        this.closeButton.on('click', this.closeOverlay.bind(this));
        $(document).on("keydown", this.keyPressDispatcher.bind(this));

        //keydown fires too quickly
        this.searchField.on("keyup", this.typingLogic.bind(this))
    }

    openOverlay() {
        //will not add the className multiple times
        this.searchOverlay.addClass("search-overlay--active");
        $("body").addClass("body-no-scroll");
        this.isOverlayOpen = true;
        //this looks hack
        setTimeout(() => this.searchField.trigger("focus"), 300);

        //prevent the default behaviour of link elements
        return false;
    }

    closeOverlay() {
        this.searchOverlay.removeClass("search-overlay--active");
        $('body').removeClass('body-no-scroll');
        this.isOverlayOpen = false;
    }

    //this functionality seems strange for a search
    //how would users know to press 's'
    keyPressDispatcher(e) {
        /* 
        //pressing 's' on any page opens the search - so stupid
        if (e.keyCode === 83 && !this.isOverlayOpen) this.openOverlay();
        if (e.keyCode === 27 && this.isOverlayOpen) this.closeOverlay();
        */
    }

    getResults() {

        $.getJSON(
            MYSCRIPT.site_url + '/wp-json/university/v1/search?term=' + this.searchField.val(),
            (results) => {
                //arrow function does not change the value of "this"
                this.resultsDiv.html(`
                <div class="row">
                    <div class="one-third">
                        <h2 class="search-overlay__section-title">General Information</h2>
                         ${results.generalInfo.length ? `<ul class="link-list min-list">` : `<p>Not found</p>`}                    
                         ${results.generalInfo.map((item) => `<li><a href="${item.link}">${item.title}</a>${item.authorName ? ` by ${item.authorName}` : ``}</li>`).join('')}
                        ${results.generalInfo.length ? '</ul>' : ''}
                    </div>
                    <div class="one-third">
                        <h2 class="search-overlay__section-title">Programs</h2>
                        ${results.programs.length ? `<ul class="link-list min-list">` : `<p>Not found</p>`}                    
                         ${results.programs.map((item) => `<li><a href="${item.link}">${item.title}</a></li>`).join('')}
                        ${results.programs.length ? '</ul>' : ''}

                        <h2 class="search-overlay__section-title">Professors</h2>
                        ${results.professors.length ? `<ul class="professor-cards">` : `<p>Not found</p>`}                    
                         ${results.professors.map((item) => `
                             <li class="professor-card__list-item">
                                <a class="professor-card" href="${item.permalink}">
                                    <img class="professor-card__image" src="${item.image}">
                                    <span class="professor-card__name">${item.title}</span>
                                </a>
                            </li>
                            `).join('')}
                        ${results.professors.length ? '</ul>' : ''}
                    </div>
                    <div class="one-third">
                        <h2 class="search-overlay__section-title">Campuses</h2>
                        ${results.campuses.length ? `<ul class="link-list min-list">` : `<p>Not found</p>`}                    
                         ${results.campuses.map((item) => `<li><a href="${item.link}">${item.title}</a></li>`).join('')}
                        ${results.campuses.length ? '</ul>' : ''}

                        <h2 class="search-overlay__section-title">Events</h2>
                        ${results.events.length ? `<ul class="link-list min-list">` : `<p>Not found</p>`}                    
                         ${results.events.map((item) => `
                            <div class="event-summary">
                            <a class="event-summary__date t-center" href="${item.permalink}">
                                <span class="event-summary__month">${item.month}</span>
                                <span class="event-summary__day">${item.day}</span>
                            </a>
                            <div class="event-summary__content">
                                <h5 class="event-summary__title headline headline--tiny">
                                <a href="${item.permalink}">${item.title}</a>
                                </h5>
                                <p>
                                ${item.description}
                                <a href="${item.permalink}" class="nu gray">Learn more</a>
                                </p>
                            </div>
                            </div>
                            `).join('')}
                        ${results.events.length ? '</ul>' : ''}
                    </div>
                </div>
            `
                );
                this.isSpinnerVisible = false;
            })
    }

    typingLogic(e) {
        let currentInput = this.searchField.val();
        if (currentInput === this.previousValue) return;

        if (currentInput === '') {
            clearTimeout(this.typingTimer);
            this.resultsDiv.html('');
            this.isSpinnerVisible = false;
        } else {
            if (!this.isSpinnerVisible) {
                this.resultsDiv.html('<div class="spinner-loader"></div>')
                this.isSpinnerVisible = true;
            }
            clearTimeout(this.typingTimer);
            this.typingTimer = setTimeout(() => {
                this.getResults();
                this.isSpinnerVisible = false;
            }, 500);
        }
        this.previousValue = currentInput;
    }

    //code like this is why react was created
    addSearchHTML() {
        $("body").append(`
                <div class="search-overlay">
      <div class="search-overlay__top">
        <div class="container">
        <i class="fa fa-search search-overlay__icon" aria-hidden="true"></i>
          <input 
            type="text"
            class="search-term"
            placeholder="What are you looking for?"
            autocomplete="off"
            id="search-term"
          />
          <i class="fa fa-window-close search-overlay__close" aria-hidden="true"></i>
        </div>
      </div>
      <div class="container">
        <div id="search-overlay__results"></div>
      </div>
    </div>
    `);
    }

}

export default Search;