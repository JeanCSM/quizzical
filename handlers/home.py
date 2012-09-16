#!/usr/bin/env python

# Portions copyright 2007 Google Inc.
#
# Licensed under the Apache License, Version 2.0 (the "License");
# you may not use this file except in compliance with the License.
# You may obtain a copy of the License at
#
#     http://www.apache.org/licenses/LICENSE-2.0
#
# Unless required by applicable law or agreed to in writing, software
# distributed under the License is distributed on an "AS IS" BASIS,
# WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
# See the License for the specific language governing permissions and
# limitations under the License.

from google.appengine.ext import webapp
from google.appengine.ext.webapp import util

from base import BaseHandler
from models import Quiz, Attempt

class HomeHandler(BaseHandler):
    def get(self):
        # Making sure that the user's profile information is fully filled in
        self.validate()
        
        # Normal users are restricted to only viewing published quizzes
        quizzes_query = Quiz.all()
        quizzes_query.filter("is_deleting =", False)
        if not self.is_admin:
            quizzes_query.filter("is_published =", True)
        self.values['quizzes'] = quizzes_query.fetch(20)
    
        # Any user can see their own scores
        attempts_query = Attempt.all()
        attempts_query.filter("user =", self.user)
        attempts_query.filter("is_archived =", False)
        attempts_query.order('-created')
        self.values['attempts'] = attempts_query.fetch(3)
    
        self.output("home.html")


def main():
    application = webapp.WSGIApplication([('/', HomeHandler)],
                                         debug=True)
    util.run_wsgi_app(application)


if __name__ == '__main__':
    main()
