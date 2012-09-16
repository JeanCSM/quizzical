class Quiz:
    @staticmethod
    def take(id):
        return '/quiz/take/%d' % int(id)
    
    @staticmethod
    def edit(id):
        return '/quiz/edit/%d' % int(id)
    
    @staticmethod
    def roster(id):
        return '/quiz/roster/%d' % int(id)
    
    @staticmethod
    def my_score(id):
        return '/quiz/my_score/%d' % int(id)
    
    @staticmethod
    def delete(id):
        return '/quiz/delete/%d' % int(id)
        
    @staticmethod
    def add():
        return '/quiz/add'
    
    @staticmethod
    def attempt(id):
        return '/quiz/attempt/%d' % int(id)

class Question:
    @staticmethod
    def add(quiz_id):
        return '/question/add/%d' % int(quiz_id)
        
    @staticmethod
    def edit(question_id):
        return '/question/edit/%d' % int(question_id)
    
    @staticmethod
    def delete(question_id):
        return '/question/delete/%d' % int(question_id)