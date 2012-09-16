from google.appengine.ext import webapp
from google.appengine.ext import db
from google.appengine.ext.webapp import util
from google.appengine.ext.webapp import template
from google.appengine.ext.db import djangoforms

from base import BaseHandler
from models import Profile, Score

class ProfileForm(djangoforms.ModelForm):
    class Meta:
        model = Profile
        exclude = ['user', 'created']
    
class ProfileHandler(BaseHandler):
    values = {
        'nav_selected': 'Profile',
        'prompt': 'In order for us to know that you submitted your test '
            'results, you\'ll need to fill in the following bits of '
            'information about yourself.',
    }
    
    def initialize(self, request, response):
        BaseHandler.initialize(self, request, response)
        self.values['action'] = self.request.url
    
    def get(self):
        profile_entity = Profile.get_for_user(self.user)
        self.values['form'] = ProfileForm(instance=profile_entity)
        self.output('user_profile.html')
    
    def post(self):
        profile_entity = Profile.get_for_user(self.user)
        form = ProfileForm(data=self.request.POST, instance=profile_entity)
    
        if form.is_valid():
            profile_entity = form.save(commit=False)
            profile_entity.user = self.user
            profile_entity.put();
        
        self.redirect('/user/me/profile')

class ScoresHandler(BaseHandler):
    def get(self):
        self.values['scores'] = Score.get_for_user(self.user)
        self.output('user_scores.html')

def main():
    application = webapp.WSGIApplication([
        ('/user/me/profile', ProfileHandler),
        ('/user/me/scores', ScoresHandler)
    ], debug=True)
    util.run_wsgi_app(application)

if __name__ == '__main__':
    main()