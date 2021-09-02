# Treasure Hunt
<img alt="preview" style="width:85%;" src="https://i.imgur.com/UaXRphE.gif">

### Installation
- Make sure that you are installed PHP in your local machine.
- Fork into your repository or clone this repository.
- Go to the folder named `Treasure-Hunt`
	
	> $ `cd Treasure-Hunt`

### How to Play
This program work only using command line tools, when you accessing through browser you'll get like this

<img alt="preview" style="width:85%;" src="https://i.imgur.com/fyGIi3m.png">

- Open command line tools, then run with following command

	> $ `php index.php`
	
	if you want to show the treasure location, you just run the following command
	 > $ `php index.php treasure`

- input steps of Up/North then enter
- input steps of Right/East then enter
- input steps of Down/South then enter

you'll see the result with probable treasure locations marked with a **$** symbol.


<pre>

============================
Probable Treasure Locations: 
[[2,5],[3,2],[3,3],[3,4],[4,3],[4,4],[4,5],[4,6]]

Treasure Location: [2,6]

Wohooo!!! Congratulations, You found the treasure...
  #  #  #  #  #  #  #  #
  #  <span style="color:red">X</span>  <span style="color:red">X</span>  <span style="color:red">X</span>  <span style="color:red">X</span>  <span style="color:red">X</span>  <span style="color:red">X</span>  #
  #  <span style="color:red">X</span>  #  #  #  $  <span style="color:green">X</span>  #
  #  <span style="color:red">X</span>  $  $  $  #  <span style="color:red">X</span>  #
  #  <span style="color:red">X</span>  #  $  $  $  $  #
  #  #  #  #  #  #  #  #
  
</pre>


###How to run with docker
first create an image
```
docker build -t evermos/treasure-hunt:latest .

```

run that image with following command 

```
docker run --name treasure-hunt --rm -i -t evermos/treasure-hunt:latest

```