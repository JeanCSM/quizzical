from google.appengine.ext import webapp
from google.appengine.ext import db
from google.appengine.ext.webapp import util
from google.appengine.ext.webapp import template
from google.appengine.ext.db import djangoforms
from django import forms

import os

from base import BaseHandler
import links
from models import Quiz, Question

class QuestionForm(djangoforms.ModelForm):
    class Meta:
        model = Question
        exclude = ['first_version', 'last_version', 'quiz']
    
    prompt = forms.CharField(
        widget = forms.TextInput()
    )

class QuestionHandler(BaseHandler):
    def fetch_quiz(self, quiz_id):
        self.values['quiz_id'] = quiz_id
        self.quiz_entity = Quiz.get_by_id(int(quiz_id))
        
    def fetch_question(self, id):
        self.values['id'] = id
        self.question_entity = Question.get_by_id(int(id))

class AddHandler(QuestionHandler):
    def get(self, quiz_id):
        self.fetch_quiz(quiz_id)
        
        self.values['form'] = QuestionForm()
        self.values['action'] = links.Question.add(quiz_id)
        self.values['back'] = links.Quiz.edit(quiz_id)
        self.output('question_edit.html')
        
    def post(self, quiz_id):
        self.fetch_quiz(quiz_id)
        
        form = QuestionForm(data=self.request.POST)
        self.values['form'] = form
        self.values['action'] = links.Question.add(quiz_id)
        
        if (form.is_valid()):
            question_entity = form.save(commit=False);
            
            order = self.quiz_entity.max + 1
            
            question_entity.quiz = self.quiz_entity
            question_entity.order = order
            question_entity.put()
            
            self.quiz_entity.version += 1
            self.quiz_entity.max = order
            
            self.quiz_entity.put()
            
            self.redirect(links.Quiz.edit(quiz_id))
        else:
            self.output('question_edit.html')

class EditHandler(QuestionHandler):
    def get(self, id):
        self.fetch_question(id)
        self.values['form'] = QuestionForm(instance=self.question_entity)
        self.values['action'] = links.Question.edit(int(id))
        self.output('question_edit.html')
    
    def post(self, id):
        self.fetch_question(id)
        form = QuestionForm(data=self.request.POST, instance=self.question_entity)
        self.values['action'] = links.Question.edit(int(id))
        self.values['form'] = form
        
        if form.is_valid():
            question_entity = form.save()
            quiz_entity = question_entity.quiz
            quiz_entity.version += 1
            quiz_entity.put()
            
            self.redirect(question_entity.quiz.link_edit())
        else:
            self.output('question_edit.html')

class DeleteHandler(QuestionHandler):
    def get(self, id):
        self.fetch_question(id)
        
        self.values['action'] = links.Question.delete(int(id))
        self.values['prompt'] = \
            'Are you absolutely sure that you would like to delete the ' \
            'question, "%s," forever?' % self.question_entity.prompt 
        self.values['back'] = os.environ['HTTP_REFERER']
        self.output('confirm.html')
    
    def post(self, id): 
        self.fetch_question(id)
        
        quiz_entity = self.question_entity.quiz
        edit_page = quiz_entity.link_edit()
        quiz_entity.version += 1;
        quiz_entity.put()
        
        self.question_entity.delete()
        self.redirect(edit_page)

def main():
    application = webapp.WSGIApplication([
        (r'/question/add/(.*)', AddHandler),
        (r'/question/edit/(.*)', EditHandler),
        (r'/question/delete/(.*)', DeleteHandler)
    ], debug=True)
    util.run_wsgi_app(application)

if __name__ == '__main__':
    main()