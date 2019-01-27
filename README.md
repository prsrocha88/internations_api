internations_api
================

A Symfony project.

## Installation

* Clone Repository ```git@gitlab.e-goi.com:theagency/zippy-mo-vouchers.git```
* Pull vendor dependencies:
 ```composer install```

## Routes

- GET /users - get all users
- GET /users/{id} - get specific user by id
- POST /users - create new user (params: { name })
- DELETE /users/{id} - remove specific user
- GET /groups - get all groups
- GET /groups/{id} - get specific group by id
- POST /groups - create new group (params: { name })
- DELETE /groups/{id} - remove specific group
- POST /users/groups - assign user to a group (params: { user_id, group_id })
- DELETE /users/{user_id}/groups/{group_id} - remove user from a group

