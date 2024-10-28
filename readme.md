 # Worktree Serve

This is a cli app that creates a worktree of an existing repo in another folder and allows you to serve it in an opinionated way.

Usage:
  `worktree-serve`
or
  `worktree-serve /path/to/repo`

## create worktree
  After setting the repo folder you can create a worktree by selecting `Create a new worktree`
    You will then be asked the folder to create the worktree in and the branch to checkout.
    The worktree will be created.
    
Things to note
- if you create a worktree in a folder that already exists, you will receive an error. You must choose a folder that does not exist.
- If you try to create a worktree of a branch already checked out in another worktree, you will receive an error. You must choose a different branch.

## Build Options

- ### Copy Env
  This will copy the `.env` file from the repo to the worktree folder. This is useful if you have environment variables that you need to use in the worktree.

- ### Run Composer
    This will run `composer install` in the worktree folder. This is useful if you have a php project and need to install dependencies.
- ### Run Npm Install
    This will run `npm install` in the worktree folder. This is useful if you have a node project and need to install dependencies.
- ### Run Npm Build
    This will run `npm run build` in the worktree folder. This is useful if you have a node project and need to build the project.
- ### Serve
    This will run `php artisan serve` in the worktree folder. This is useful if you have a php project and need to serve it locally.
- ### Port
    This is the port that the server will run on. The default is 42069.

## Errors

If the application fails you might have to clean up the worktree folder manually. 
The worktree folder can be found by running `git worktree list` from the main repo folder.
You can delete this folder and and run `git worktree prune` to remove the worktree from the repo.
Alternatively you can run `git worktree remove <path to worktree> --force` to remove the worktree.
