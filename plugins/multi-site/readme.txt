http://www.jerseyconnect.net/development/multisite-faq/

Multi-Site Manager FAQ

All technical directions will assume that Apache is the web server behind any WPMU install. If this is not true in your case, you will have to find the equivalent term or function for your particular server product.
General Questions

What is the Multi-Site Manager?
    Multi-Site Manager is a plugin for WPMU that allows anyone to run blogs on multiple domains with a single install.
Is the Multi-Site Manager right for me?
    If you run blogs on one domain with WPMU and want to run new blogs on another domain, Multi-Site Manager can help you.
    If you want a single blog to be accessible by more than one address, it can NOT.
    If you want different VHOST policies per-domain, it can NOT.
    If you want to add hundreds of domains at a time, you would be better served by a Perl script.
What is a “site” in WPMU?
    A WPMU site is a domain.
    If you are using subdomains, a site is a domain suffix (e.g. myblogs.com)
    If you are using subdirectories, a site is a fully-qualified domain name (e.g. blogs.hostname.com).
Where do I download Multi-Site Manager?
    The most up-to-date version of Multi-Site Manager is always available at wpmudev.org.

Installation & Operation

How do I install it?
    Copy the file into your mu-plugins directory.
How do I set up my domains to work with it?
    Each domain you want to administer with a single WPMU install must be pointed by your webserver at your WPMU installation files. This means that:

       1. DNS should resolve each domain to your webserver’s address
       2. Your webserver should serve the WPMU files when someone requests that domain ( via a new VirtualHost with appropriately set DocumentRoot, or with a ServerAlias directive )
       3. If you create a new VirtualHost and use per-VirtualHost Directory statements, ensure that the new VirtualHost’s Directory statement is configured to match the old one.

How do I create a new site?

       1. Log in to your current site with a site administrator account.
       2. Go to the Site Admin menu. Go into the Sites tab that has been created there.
       3. Enter the basic information for the new site in the form at the bottom of the page.
       4. You will generally want to ‘clone’ your first site. See What happens when I create a site? and What is cloning? below.
       5. Select any sitemeta keys you want to create on the new site.
       6. Click Create Site

How do I manage my new site?
    Go to any blog backend on the new site. As a shortcut, click the number under the Blogs column in your new site’s row.
What is “cloning?”
    Cloning is the process of copying sitemeta keys and values from one site to another. Blogs and other blog-specific settings are not copied when cloning.

Advanced Topics

What happens when I create a new site?
    Here is a brief rundown of what happens when a new site is created:

       1. A new entry is created in the sites table.
       2. A new blog is created with the wpmu_create_blog function at the domain and path that were selected.
       3. The specified site name is applied to the new site.
       4. If a site was selected to clone, any selected sitemeta values are copied to the new site.

Can I use Multi-Site Manager for sites with subdirectories?
    Absolutely. It was originally created for sites with subdirectories.
How do I have different site administrators for different sites?
    WPMU has a shared user pool for all blogs and all sites. This means that it is not generally a good idea to give control of one or more sites to an untrusted party, as full isolation cannot be achieved. With this in mind, setting different site admins is both technically possible and easy to accomplish:

       1. Create a new site as usual.
       2. Manage the new site,and change the value for Site Admins under the site Options menu.

    If you removed your current login from this list, you will get an error message. However, the change has been committed.
How do I create a site on a non-root path?
    This is easy to configure in WPMU, but difficult to configure in Apache. You will need to adjust the .htaccess file in your WPMU directory (or corresponding directive in httpd.conf) to prepend the path to requests for that domain. If you are not very familiar with mod_rewrite, you should be prepared for slow going.
Does my blog_id need to match my site_id?
    No. This implies a one-to-one relationship between sites and blogs, which is not the usual case. Even when there is only one blog per site, they do not need to match. That is why there is a column for each in the blogs table.
What action and filter hooks are available for site actions?
    See the API extensions page for hook details.

