from google.appengine.ext import webapp
from google.appengine.ext import db
from google.appengine.ext.webapp import util
from google.appengine.ext.webapp import template
from google.appengine.ext.db import djangoforms

from django.utils import simplejson

from base import BaseHandler, BaseView, QuizHandler
import links
from models import *

class AttemptView(BaseView):
    attempt = None
    template = 'quiz_attempt.html'
    
    def __init__(self, attempt, handler):
        BaseView.__init__(self, handler);
        self.attempt = attempt
        
        template.register_template_library('filters.resolve')
        
        self.values['attempt'] = attempt
        self.values['quiz'] = attempt.quiz
        self.values['quiz_snapshot'] = attempt.snapshot.quiz_entity
        self.values['questions'] = attempt.snapshot.questions
        self.values['responses'] = attempt.responses

class TakeHandler(QuizHandler):
    def get(self, id):
        self.fetch(id)
        self.values['action'] = links.Quiz.take(int(id))
        self.values['questions'] = Question.get_for_quiz(self.quiz_entity)
        self.output('quiz_take.html')
    
    def post(self, id):
        self.fetch(id)
        
        score = self.quiz_entity.related_score(self.user)
        
        if score is None:
            score = Score()
            score.user = self.user
            score.quiz = self.quiz_entity
        
        if self.quiz_entity.is_limited == True and \
                    score.attempts >= self.quiz_entity.attempts:
            error(403)
            # TODO: Thow a message saying that they're over the max number of
            #       allowed attempts per quiz
            return
        
        questions = Question.get_for_quiz(self.quiz_entity)
        
        responses = {}
        correct = 0
        total = 0
        
        for question in questions:
            numeric_id = question.key().id()
            id = 'question-%d' % numeric_id
            try:
                value = int(self.request.get(id))
            except ValueError:
                value = -1
                
            if (question.correct == value):
                correct += 1
            
            responses[numeric_id] = value
            total += 1
        
        if not Snapshot.exists(self.quiz_entity, self.quiz_entity.version):
            snapshot = Snapshot()
            snapshot.version = self.quiz_entity.version
            snapshot.quiz = self.quiz_entity
            snapshot.quiz_entity = self.quiz_entity
            snapshot.questions = questions
            snapshot.put()
        else:
            snapshot = Snapshot.get_for_quiz_version(self.quiz_entity,
                                                     self.quiz_entity.version)
        
        attempt = Attempt()
        attempt.user = self.user
        attempt.quiz = self.quiz_entity
        attempt.snapshot = snapshot
        attempt.correct = correct
        attempt.total = total
        attempt.responses = responses
        attempt.put()
        
        if attempt.percentage() >= score.percentage():
            score.correct = correct
            score.total = total
        
        score.attempts += 1
        
        profile = Profile.get_for_user(self.user)
        score.first_name = profile.first_name
        score.last_name = profile.last_name
        
        score.put()
        
        view = AttemptView(attempt, self)
        view.output()

class AttemptHandler(BaseHandler):
    def get(self, id):
        attempt = Attempt.get_by_id(int(id))
        
        if attempt is None:
            self.error(404)
            return

        if (not self.is_admin) and (attempt.user is not self.user):
            print attempt.to_xml()
            self.error(403)
            return

        if attempt.is_archived:
            self.error(403)
            return
        
        view = AttemptView(attempt, self)
        view.output()

class MyScoreHandler(QuizHandler):
    def get(self, id):
        self.fetch(id)
        
        self.values['score'] = Score.get_for_quiz_user(self.quiz_entity,
                                                       self.user)
        
        self.output('quiz_my_score.html')

def main():
    application = webapp.WSGIApplication([
        (r'/quiz/take/(.*)', TakeHandler),
        (r'/quiz/attempt/(.*)', AttemptHandler),
        (r'/quiz/my_score/(.*)', MyScoreHandler)
    ], debug=True)
    util.run_wsgi_app(application)

if __name__ == '__main__':
    main()