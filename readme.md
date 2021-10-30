- [x] fetching questions from json
- [x] fetching questions from csv
- [x] fetching questions with api
- [x] questions translation
- [x] fetching translated questions with api
- [x] adding new questions
- [x] endpoint for adding new question
- [ ] handling big json files
- [ ] handling concurrent json writing
- [ ] handling big csv files
- [ ] handling concurrent csv writing

this is a technical exercise for job assessment (php backend developer position)

you can read task [here](task.md)

## How to run:

```shell
make up
```

service will be available at http://localhost:8080

### configuration:

main configuration happens at [app/Providers/QuestionsServiceProvider.php](app/Providers/QuestionsServiceProvider.php) -
there you can easily swap repository implementations and translator engine implementations.

also you can change expected path to files via env variables:

```dotenv
CSV_PATH=
JSON_PATH=
```

for api documentation check [open-api.yml](open-api.yaml)