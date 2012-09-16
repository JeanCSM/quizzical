from django import template as django_template

register = django_template.Library()

@register.filter
def resolve(value, key):
    return value[str(key)]