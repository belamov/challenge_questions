[![tests](https://github.com/belamov/challenge_questions/actions/workflows/main.yml/badge.svg)](https://github.com/belamov/challenge_questions/actions/workflows/main.yml)

this is a technical exercise for assessment of my skills for backend php developer position

you can watch me livecoding this excercise from scratch [here](https://www.youtube.com/watch?v=T0CjQw54tP0)

you can read task [here](task.md)

### how to run:

```shell
make up
```

service will be available at http://localhost:8000

### configuration:

you can configure application via env variables

```dotenv
FILE_PATH='path to file database'
FILE_FORMAT='csv|json'
```

for api documentation check [open-api.yml](open-api.yaml)

### todo:

- [x] fetching questions from json
- [x] fetching questions from csv
- [x] fetching questions with api
- [x] questions translation
- [x] fetching translated questions with api
- [x] adding new questions
- [x] endpoint for adding new question
- [x] make composer-require-checker pass
- [x] add other checks (except tests) to ci - move ci to docker
- [ ] validation of language query parameter - it must be ISO-639-1 code
- [ ] ??? handling concurrent json writing
- [ ] ??? handling concurrent csv writing
