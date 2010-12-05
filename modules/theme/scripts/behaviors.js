// Copyright 2010 Jonathan Wilde. This code released according to Mozilla's
// MPL1.1-LGPL-GPL trilicense.

// Make fields have placeholders in browsers that support that feature; we're
// not doing full placeholders for sake of simplicity.
window.addEvent('domready', function () {
    if ('placeholder' in document.createElement('input'))
    {
        $$('input[type=text], input[type=password], textarea').each(function (e)
        {
            var label = e.getPrevious('label');
            e.set('placeholder', label.get('text'));
            label.dispose();
        });
    }
});
