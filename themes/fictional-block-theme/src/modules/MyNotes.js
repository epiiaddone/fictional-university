import $ from 'jquery';

class MyNotes {

    constructor() {
        this.events();
    }


    events() {
        //add event listener to the ul
        $("#my-notes").on("click", ".delete-note", this.deleteNote);
        $("#my-notes").on("click", ".edit-note", this.editNote.bind(this));
        $("#my-notes").on("click", ".update-note", this.updateNote.bind(this));
        $(".submit-note").on("click", this.createNote.bind(this))
    }

    deleteNote(e) {
        let thisNote = $(e.target).parents("li");
        $.ajax({
            beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', MYSCRIPT.nonce)
            },
            url: MYSCRIPT.site_url + '/wp-json/wp/v2/note/' + thisNote.data("id"),
            type: 'DELETE',
            success: (response) => {
                if (response.userNoteCount < 5) $('.note-limit-message').removeClass("active");
            },
            error: (response) => { console.log('error', response) }
        });
    }

    editNote(e) {
        let thisNote = $(e.target).parents("li");
        if (thisNote.data("state") == "editable") {
            this.makeNoteReadOnly(thisNote)
        } else {
            this.makeNoteEditable(thisNote)
        }
    }

    makeNoteEditable(thisNote) {
        thisNote.find(".edit-note").html('<i class="fa fa-times" aria-hidden="true"></i> Cancel')
        thisNote.find(".note-title-field, .note-body-field").removeAttr("readonly").addClass("note-active-field")
        thisNote.find(".update-note").addClass("update-note--visible")
        thisNote.data("state", "editable")
    }

    makeNoteReadOnly(thisNote) {
        thisNote.find(".edit-note").html('<i class="fa fa-pencil" aria-hidden="true"></i> Edit')
        thisNote.find(".note-title-field, .note-body-field").attr("readonly", "readonly").removeClass("note-active-field")
        thisNote.find(".update-note").removeClass("update-note--visible")
        thisNote.data("state", "notEditable")
    }

    updateNote(e) {
        let thisNote = $(e.target).parents("li");
        let updatedNote = {
            'title': thisNote.find(".note-title-field").val(),
            'content': thisNote.find(".note-body-field").val()
        }

        $.ajax({
            beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', MYSCRIPT.nonce)
            },
            url: MYSCRIPT.site_url + '/wp-json/wp/v2/note/' + thisNote.data("id"),
            type: 'POST',
            data: updatedNote,
            success: (response) => { this.makeNoteReadOnly(thisNote) },
            error: (response) => { console.log('update error', response) }
        });
    }

    createNote() {
        let newNote = {
            'title': $(".new-note-title").val(),
            'content': $(".new-note-body").val(),
            'status': 'private'
        }

        $.ajax({
            beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', MYSCRIPT.nonce)
            },
            url: MYSCRIPT.site_url + '/wp-json/wp/v2/note/',
            type: 'POST',
            data: newNote,
            success: (response) => {
                $(".new-note-title, .new-note-body").val('')
                $(`
                    <li data-id="${response.id}">
                <input readonly value="${response.title.raw}" class="note-title-field">
                <span class="edit-note"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</span>
                <span class="delete-note"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</span>
                <textarea readonly class="note-body-field">${response.content.raw}</textarea>
                <span class="update-note btn btn--blue btn--small"><i class="fa fa-arrow-right" aria-hidden="true"></i> Save</span>
                </li>
        `).prependTo("#my-notes").hide().slideDown()
            },
            error: (response) => {
                //this is very fragile
                //it would be much better to use error codes
                if (response.responseText == 'You have reached you note limit') {
                    $(".note-limit-message").addClass("active");
                }
            }
        });
    }
}

export default MyNotes;