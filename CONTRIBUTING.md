# Contributing to Denton Bible Church

## Pull requests

Make changes yourself and submit them as pull requests for consideration to be
added to the live site.

Please follow this process; it's the best way to get your work included in the
project:

1. [Fork](http://help.github.com/fork-a-repo/) the project, clone your fork,
   and configure the remotes:

   ```bash
   # Clones your fork of the repo into the current directory in terminal
   git clone https://github.com/<your-username>/Website.git
   # Navigate to the newly cloned directory
   cd Website
   # Assigns the original repo to a remote called "upstream"
   git remote add upstream https://github.com/Denton-Bible-Church/Website.git
   ```

2. If you cloned a while ago, get the latest changes from upstream:

   ```bash
   git checkout master
   git pull upstream master
   ```

3. Create a new topic branch to contain your feature, change, or fix:

   ```bash
   git checkout -b <topic-branch-name>
   ```

4. Commit your changes in logical chunks. Please adhere to these [git commit
   message
   guidelines](http://tbaggery.com/2008/04/19/a-note-about-git-commit-messages.html)
   or your pull request is unlikely be merged into the main project. Use git's
   [interactive rebase](https://help.github.com/articles/interactive-rebase)
   feature to tidy up your commits before making them public.

5. Locally merge (or rebase) the upstream development branch into your topic branch:

   ```bash
   git pull [--rebase] upstream master
   ```

6. Push your topic branch up to your fork:

   ```bash
   git push origin <topic-branch-name>
   ```

10. [Open a Pull Request](http://help.github.com/send-pull-requests/) with a
    clear title and description.
