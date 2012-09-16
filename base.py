import os

from google.appengine.api import users
from google.appengine.ext import webapp
from google.appengine.ext.webapp import template

from models import Profile
import links

class BaseHandler(webapp.RequestHandler):
    values = {}
    
    def __init__(self):
        webapp.RequestHandler.__init__(self)
        
        self.user = users.get_current_user()
        self.is_admin = users.is_current_user_admin()
    
    def validate(self):
        if self.user and not Profile.exists(self.user):
            self.redirect('/user/me/profile')
    
    def output(self, name):
        defaults = {
            'is_admin': self.is_admin,
            'is_logged_in': self.user != None,
            'user': self.user,
            'profile': "/user/me/profile",
            'sign_out': users.create_logout_url('/'),
            'links': links,
        }
        
        data = dict(defaults.items() + self.values.items())
        path = os.path.join(os.path.dirname(__file__), 'templates', name)
        self.response.out.write(template.render(path, data))

class BaseView():
    values = {}
    handler = None
    template = None

    def __init__(self, handler, template = None):
        self.handler = handler
        if template != None:
            self.template = template
    
    def set(key, value):
        values[key] = value
    
    def output(self):
        defaults = {
            'is_admin': self.handler.is_admin,
            'is_logged_in': self.handler.user != None,
            'user': self.handler.user,
            'profile': "/user/me/profile",
            'sign_out': users.create_logout_url('/'),
            'links': links,
        }
        
        data = dict(defaults.items() + self.values.items())
        path = os.path.join(os.path.dirname(__file__), 'templates', name)
        self.handler.response.out.write(template.render(path, data))

class QuizHandler(BaseHandler):
    def fetch(self, id):
        from models import Quiz
        
        self.values['id'] = id
        self.quiz_entity = Quiz.get_by_id(int(id))
        self.values['quiz'] = self.quiz_entity
