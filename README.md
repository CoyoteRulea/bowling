# __Bowling Scores Script__
## **_Requirements_**
In order to run the following app is required to install the following requirements:
* PHP 8.1 Installed in your test environment.
* Composer 2 Installed in your test environment.


`If you don't want to install composer at local enviroment, please feel free to use our Docker container.`
* You need to have Docker installed and running in your local environment. [For more info click here](https://docs.docker.com/get-docker/)
* Before step three you need to run build command with:
```
docker-compose up -d --build  
```
* And go to docker console with:
```
docker exec -it phpfpm-bownling sh  
```
* Continue with step three.

## Script instalation
1. Clone this repository with:
```
git clone https://github.com/CoyoteRulea/bowling.git
```
2. Go to bowling folder with: `cd bowling`
3. Run: `composer install`
   - If something goes wrong with your installation, probably you need to add the requested modules in your `php.ini` file for CLI.

## Script Usage
In order to show list commands use `bin/console list`
* For Windows please use the prefix command `php bin/console list`

### 1. **Run Scoreboard script**
```
bin/console app:bowling-cli "path/to/filename"
```                                       
or
```
bin/console app:run "path/to/filename"
```
### _Output Example:_
![ScoreBoard Display Output Example](https://raw.githubusercontent.com/CoyoteRulea/bowling/main/assets/images/screenshot-scoreboard.png)
### 2. **Run Scoreboard Easy To Read script**
```
bin/console app:bowling-cli "path/to/filename" --prettyfied
```                                       
or
```
bin/console app:run "path/to/filename" --prettyfied
```
### _Output Example:_
![ScoreBoard Display Output Example](https://raw.githubusercontent.com/CoyoteRulea/bowling/main/assets/images/screenshot-scoreboard-pretty.png)

### 3. **Run PHP Unit Tests**
- In order to run all defined PHP Unit Test please run:
```
bin\console app:tests
```
- In order to run specific PHP Unit Test please add `-t` or `--testlist` followed by test names separated by comma:
```
bin\console app:tests -t usertest,usertest2
```
### _Output Example:_
![PHP Unit Test Output Example](https://raw.githubusercontent.com/CoyoteRulea/bowling/main/assets/images/screenshot-tests.png)

## License
This repository is covered by [The Unlicensed](LICENSE.md)[^license] license.

[^license]:
    This project was created by @CoyoteRulea :+1: and never was supported or maintained. Use it only for review or maybe some educational purposes.