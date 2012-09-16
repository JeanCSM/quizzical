from google.appengine.ext import db
from django.utils import simplejson
import links

class Profile(db.Model):
    user = db.UserProperty()
    first_name = db.StringProperty(required=True)
    last_name = db.StringProperty(required=True)
    created = db.DateTimeProperty(auto_now_add=True)
    
    @staticmethod
    def get_for_user(user):
        query = db.Query(Profile)
        query.filter('user =', user)
        return query.get()
    
    @staticmethod
    def exists(user):
        query = db.Query(Profile, keys_only=True)
        query.filter('user =', user)
        return query.get() != None

class Quiz(db.Model):
    title = db.StringProperty(required=True)
    prompt = db.StringProperty()
    is_published = db.BooleanProperty()
    is_limited = db.BooleanProperty()
    attempts = db.IntegerProperty(required=False)
    updated = db.DateTimeProperty(auto_now=True)
    version = db.IntegerProperty(default=1)
    max = db.IntegerProperty(default=0)
    
    _score = False
    
    def link_take(self):
        return links.Quiz.take(self.key().id())
    
    def link_edit(self):
        return links.Quiz.edit(self.key().id())
    
    def link_roster(self):
        return links.Quiz.roster(self.key().id())
    
    def link_my_score(self):
        return links.Quiz.my_score(self.key().id())
    
    def link_delete(self):
        return links.Quiz.delete(self.key().id())
    
    def link_question_add(self):
        return links.Question.add(self.key().id())
    
    def related_score(self, user=None):
        if self._score is False:
            if user is None:
                from google.appengine.api import users
                user = users.get_current_user();
            
            query = db.Query(Score)
            query.filter('quiz =', self)
            query.filter('user =', user)
            self._score = query.get()
        
        return self._score
    
    def related_questions(self):
        return Question.get_for_quiz(self)

class Question(db.Model):
    quiz = db.ReferenceProperty(Quiz)
    prompt = db.TextProperty()
    choices = db.StringListProperty()
    correct = db.IntegerProperty()
    order = db.IntegerProperty()
    
    def link_delete(self):
        return links.Question.delete(self.key().id())
    
    def link_edit(self):
        return links.Question.edit(self.key().id())
    
    @staticmethod
    def get_for_quiz(quiz):
        query = db.Query(Question)
        query.filter('quiz =', quiz)
        query.order('order');
        
        return query.fetch(200)

class Snapshot(db.Model):
    quiz = db.ReferenceProperty(Quiz)
    version = db.IntegerProperty()
    quiz_state = db.TextProperty()
    questions_state = db.TextProperty()
    
    def fill_quiz(self, quiz):
        quiz_dict = {
            'title': quiz.title,
            'prompt': quiz.prompt
        }
        
        self.quiz_state = simplejson.dumps(quiz_dict)
        
    def pull_quiz(self):
        return simplejson.loads(self.quiz_state)
    
    quiz_entity = property(pull_quiz, fill_quiz)
    
    def fill_questions(self, questions):
        questions_arr = []
        
        for question in questions:
            question_dict = {
                'id': question.key().id(),
                'prompt': question.prompt,
                'choices': question.choices,
                'correct': question.correct
            }
            
            questions_arr.append(question_dict)
        
        self.questions_state = simplejson.dumps(questions_arr)
    
    def pull_questions(self):
        return simplejson.loads(self.questions_state)
    
    questions = property(pull_questions, fill_questions)
    
    @staticmethod
    def exists(quiz, version):
        query = db.Query(Snapshot, keys_only=True)
        query.filter('quiz =', quiz)
        query.filter('version =', version)
        return query.get() != None

    @staticmethod
    def get_for_quiz_version(quiz, version):
        query = db.Query(Snapshot)
        query.filter('quiz =', quiz)
        query.filter('version =', version)
        return query.get()

class Score(db.Model):
    user = db.UserProperty()
    quiz = db.ReferenceProperty(Quiz)
    correct = db.IntegerProperty()
    total = db.IntegerProperty()
    attempts = db.IntegerProperty(default=0)
    updated = db.DateTimeProperty(auto_now=True)
    first_name = db.StringProperty()
    last_name = db.StringProperty()
    
    @staticmethod
    def link_my_scores():
        return '/user/me/scores'
    
    def link_my_score():
        return 'yay'
        #return links.Quiz.my_score(self.quiz.key().id())
    
    @staticmethod
    def get_for_user(user):
        query = db.Query(Score)
        query.filter('user =', user)
        
        return query.fetch(200)
    
    @staticmethod
    def get_for_quiz_user(quiz, user):
        query = db.Query(Score)
        query.filter('user =', user)
        query.filter('quiz =', quiz)
        
        return query.get()
    
    def percentage(self):
        if (self.total > 0):
            return round(float(self.correct) / float(self.total) * 100, 1)
        else:
            return 0
    
    def related_attempts(self):
        query = db.Query(Attempt)
        query.filter('user =', self.user)
        query.filter('quiz =', self.quiz)
        
        return query.fetch(200)

class Attempt(db.Model):
    user = db.UserProperty()
    quiz = db.ReferenceProperty(Quiz)
    snapshot = db.ReferenceProperty(Snapshot)
    correct = db.IntegerProperty()
    total = db.IntegerProperty()
    responses_state = db.TextProperty()
    created = db.DateTimeProperty(auto_now_add=True)
    is_archived = db.BooleanProperty(default=False)
    
    def percentage(self):
        if (self.total > 0):
            return round(float(self.correct) / float(self.total) * 100, 1)
        else:
            return 0
        
    def fill_responses(self, responses):
        self.responses_state = simplejson.dumps(responses)
    
    def pull_responses(self):
        return simplejson.loads(self.responses_state)
    
    responses = property(pull_responses, fill_responses)
    
    def link_view(self):
        return links.Quiz.attempt(self.key().id())