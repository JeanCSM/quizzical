/* ***** BEGIN LICENSE BLOCK *****
 * Version: MPL 1.1/GPL 2.0/LGPL 2.1
 *
 * The contents of this file are subject to the Mozilla Public License Version
 * 1.1 (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Software distributed under the License is distributed on an "AS IS" basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
 * for the specific language governing rights and limitations under the
 * License.
 *
 * The Original Code is Quizzical.
 *
 * The Initial Developer of the Original Code is Jonathan Wilde.
 * Portions created by the Initial Developer are Copyright (C) 2009
 * the Initial Developer. All Rights Reserved.
 *
 * Contributor(s):
 *
 * Alternatively, the contents of this file may be used under the terms of
 * either the GNU General Public License Version 2 or later (the "GPL"), or
 * the GNU Lesser General Public License Version 2.1 or later (the "LGPL"),
 * in which case the provisions of the GPL or the LGPL are applicable instead
 * of those above. If you wish to allow use of your version of this file only
 * under the terms of either the GPL or the LGPL, and not to allow others to
 * use your version of this file under the terms of the MPL, indicate your
 * decision by deleting the provisions above and replace them with the notice
 * and other provisions required by the GPL or the LGPL. If you do not delete
 * the provisions above, a recipient may use your version of this file under
 * the terms of any one of the MPL, the GPL or the LGPL.
 *
 * ***** END LICENSE BLOCK ***** */

var Monitor = new Class({
    questions: [],
    ids: [],
    skipped: [],
    incomplete: [],
    cursor: 0,

    Implements: [Events],

    initialize: function (form) {
        // Save the reference to the quiz form and IncompleteMonitor
        this.form = form;

        // Index the questions on the page so that we can quickly determine which
        // question is which
        this.index();

        // Link up events to all of the radio buttons so that we can check the
        // location of the cursor and the question that the user just answered
        $$("input[type=radio]").addEvent("click", this.determine.bindWithEvent(this));
    },

    index: function () {
        // Get all of the questions and push them into the questions array for
        // this class
        $$(".question p").each(function (question, index) {
            this.questions.push(question.get('text'));

            // Determine the id of the question by grabbing the anchor in the
            // question and push that id into our array of all of the ids
            var id = question.getElement('a').get('name').substring(9);
            this.ids.push(parseInt(id));

            // Push the current index into the incomplete array
            this.incomplete.push(index);
        }.bind(this));

        // Fire an event stating that the indexing has been completed
        this.fireEvent('onIndexComplete');
    },

    determine: function (e) {
        // Determine the id of the question that was selected
        var name = $(e.target).get("name");
        var question = new String(name).substring(9);

        // Determine the index that we're at based on the question id
        var index = this.ids.indexOf(parseInt(question));

        // Remove this index from the incomplete items array
        this.incomplete.erase(index);

        // Determine the appropriate action based on the id of the question
        // selected and the current cursor position
        if (index == this.cursor) {
            // Since the question id equals the cursor position, we're going
            // to not do anything except increment the cursor by one
            this.cursor++;
        } else if (index > this.cursor) {
            // Since the user skipped some or more questions, push all of the
            // items between the question and the cursor into the skipped array
            for (var i = this.cursor; i < index; i++) {
                this.skipped.include(parseInt(i));
            }

            // Update the cursor location to the location of the question so
            // that the skipped questions aren't added in multiple times
            this.cursor = index + 1;
        } else if (index < this.cursor) {
            // Since the question is behind the cursor, the user must be going
            // back to do some of their missed questions; thus, we will go and
            // remove the appropriate skipped question from the skipped array
            this.skipped = this.skipped.erase(index);
        }

        // Fire an event stating that something has changed in terms of the
        // types of items that have been selected
        this.fireEvent("onChanged");
    }
});

var Skipped = new Class({
    initialize: function (monitor) {
        // Save the reference to the Monitor object
        this.monitor = monitor;

        // Generate the general layout for the sidebar block
        this.generate();

        // Update the list of skipped questions for the first time
        this.update();

        // Attach an event to our monitor so that when the monitor registers
        // an updated skip or updated incomplete, the sidebar box is updated
        this.monitor.addEvent("onChanged", this.update.bindWithEvent(this));
    },

    generate: function () {
        // Generate the block that will contain the list of skipped questions
        this.block = new Element("div", {
            "class": "aside"
        }).inject($('sidebar'), "top");

        // Make the block pinned, position-wise
        this.block.pin();

        // Generate the title for that block
        this.title = new Element("h4", {
            "text": "Skipped Questions"
        }).inject(this.block, "top");

        // Generate a content block
        this.content = new Element("div", {
            "class": "aside-content"
        }).inject(this.block, "bottom")
    },

    update: function () {
        // If there are no skipped items, set the html of the skipped questions
        // to a message stating that there are none
        if (this.monitor.skipped.length == 0) {
            if (this.monitor.incomplete.length > 0) {
                // Define the template for the no skipped items message, with
                // a note about how there are still a bunch of skipped questions
                var template = "<div class='aside-row'>You haven't skipped over " +
                               "any questions, but you still have <strong>" +
                               "{incomplete} questions</strong> to go.</div>";
            } else {
                // Define the template for the no skipped items message in a way
                // that includes the fact that all of the questions that have
                // been completed
                var template = "<div class='aside-row'>You've completed all of " +
                               "the questions.</div>";
            }

            this.content.set("html", template.substitute({
                "incomplete": this.monitor.incomplete.length
            }));''
            return;
        }

        // Clear out the content of the skipped questions list
        this.content.empty();

        // Loop through all of the skipped questions and make a list out of them
        this.monitor.skipped.each(function (id) {
            // A template for the title text
            var template = "<a href='#question-{id}'>#{id}</a> &mdash; {abbrev}";
            var abbrev = new String(this.monitor.questions[id]).substring(0, 15) + "...";

            // Create a new element for the skipped question
            new Element("div", {
                "html": template.substitute({ id: id + 1, abbrev: abbrev }),
                "class": "aside-row"
            }).inject(this.content, "bottom");
        }.bind(this));
    }
});

var Confirm = new Class({
    initialize: function (form, monitor) {
        // Save the reference to the quiz form and the Monitor object
        this.form = form;
        this.monitor = monitor;

        // Generate the initial markup for the confirmation window
        this.generate();

        // Attach a confirmation message to the the form's submit button
        this.form.addEvent("submit", this.confirm.bindWithEvent(this));
    },

    generate: function () {
        // Generate the overlay element, which will black out the background
        // to draw attention to the confirmation window
        this.overlay = new Element('div', {
            "class": "confirm-overlay"
        }).inject($(document.body), "bottom");

        // Generate the container for the confirmation window
        this.container = new Element('div', {
            "class": "confirm"
        }).inject($(document.body), "bottom");

        // Generate the title for the confirmation window
        this.title = new Element('h2', {
            "text": "Are you really sure that you want to submit your quiz?"
        }).inject(this.container, "bottom");

        // Generate the subheading for the incomplete items
        this.incompleteHeading = new Element('h3', {
            "text": "Incomplete Questions"
        }).inject(this.container, "bottom");

        // Generate the box to contain the list of incomplete items
        this.incomplete = new Element('div', {
            "class": "confirm-incomplete"
        }).inject(this.container, "bottom");

        // Generate the block to contain the submit and go back buttons
        this.submitContainer = new Element('div', {
            "class": "go-back-or-submit"
        }).inject(this.container, "bottom");

        // Insert the submit and go back buttons into the container
        this.back = new Element('a', {
            "href": "#",
            "class": "button",
            "text": "Go Back"
        }).inject(this.submitContainer, "bottom");
        this.back.addEvent("click", this.backAction.bindWithEvent(this));

        this.submit = new Element('a', {
            "href": "#",
            "text": "Submit"
        }).inject(this.submitContainer, "bottom");
        this.submit.addEvent("click", this.submitAction.bindWithEvent(this));
    },

    update: function () {
        // If there are no incomplete items, put a message in the box that
        // states that
        if (this.monitor.incomplete.length == 0) {
            this.incomplete.set("html", "<div class='incomplete-row'>There are " +
                                "no incomplete questions.</div>");
            return;
        }

        // For sake of simplicity, we're going to remove all of the items in the
        // box and then regenerate
        this.incomplete.empty();

        // We'll loop through all of the incomplete indexes and then generate
        // a list of incomplete questions
        this.monitor.incomplete.each(function (index) {
            // Define the template for the question
            var template = "#{id} &mdash; {abbrev}";
            var abbrev = new String(this.monitor.questions[index]).substring(0, 50) + "...";

            var question = new Element('div', {
                "class": "incomplete-row",
                "html": template.substitute({
                    "id": index + 1,
                    "abbrev": abbrev
                })
            }).inject(this.incomplete, "bottom");
        }.bind(this));
    },

    confirm: function (event) {
        // Stop the form from submitting
        event.stop();

        // Update the list of incomplete questions in the confirmation window
        this.update();

        // Display the confirmation window
        this.overlay.setStyle("display", "block");
        this.container.setStyle("display", "block");
    },

    backAction: function (event) {
        // Stop the page from jumping to the top
        event.stop();

        // Hide the confirmation window
        this.overlay.setStyle("display", "none");
        this.container.setStyle("display", "none");
    },

    submitAction: function (event) {
        // Stop the page from jumping to the top
        event.stop();

        // Submit the form
        this.form.submit();
    }
});

window.addEvent('domready', function () {
    // Try to grab a reference to the quiz form
    var quiz = $$(".quiz form")[0];

    // If we can get a reference to the quiz form, generate a new SkipMonitor
    // (to check to see when users skip a question and move onto the next) and
    // a new IncompleteMonitor (to display a message telling the user what
    // questions they haven't completed when they press the submit button)
    if (quiz) {
        var monitor = new Monitor(quiz);
        var skipped = new Skipped(monitor);
        var submit = new Confirm(quiz, monitor);
    }
});


/* End of file: quiz.js */
