from google.appengine.ext import webapp
from google.appengine.ext import db
from google.appengine.ext.webapp import util
from google.appengine.ext.webapp import template
from google.appengine.ext.db import djangoforms

from base import BaseHandler
from models import Profile

class ListHandler(BaseHandler):
    def get(self):
        self.validate()
        
        profiles_query = Profile.all()
        profiles_query.order('last_name')
        self.values['profiles'] = profiles_query.fetch(100)    
        # TODO: Figure out paging
        
        self.output('user_list.html')

def main():
    application = webapp.WSGIApplication([
        ('/user/list', ListHandler),
    ], debug=True)
    util.run_wsgi_app(application)

if __name__ == '__main__':
    main()