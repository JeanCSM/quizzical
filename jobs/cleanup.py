from google.appengine.ext.mapreduce import operation, context

def archive(entity):
    params = context.get().mapreduce_spec.mapper.params
    quiz_id = int(params['quiz_id'])
    if entity.quiz.key().id() == quiz_id:
        entity.is_archived = True
        yield operation.db.Put(entity)

def delete(entity):
    params = context.get().mapreduce_spec.mapper.params
    quiz_id = int(params['quiz_id'])
    if entity.quiz.key().id() == quiz_id:
        entity.is_archived = True
        yield operation.db.Delete(entity)

def dump(entity):
    yield operation.db.Delete(entity)

def poke(entity):
    yield operation.db.Put(entity)