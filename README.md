##concrete5 as a composer dependancy

**This is a proof of concept of installing concrete5 as a composer dependancy. As such, it shouldn't be used in production environments.**

It's modelled on one of the concepts that the [laravel framework](http://laravel.com) uses to create and install new laravel projects and therefore is intended to be used to create new concrete5 'projects', although you could migrate existing installations over to this concept of dependancy management.

Note that this is experimental at present, and will install v8.0.0-beta6 as well as my boilerplate-package.

## Useage

To install a fresh copy of concrete5 you run the following composer command:

    composer create-project c5labs/concrete5-composer my-site
    
This will install concrete5 into a folder named 'my-site'.

To update concrete5 to the latest development release you run (from within the 'my-site' directory):

    composer update
    
As simple as that!