## Test Application for checking monitoring via TIG stack

### Install

1. Clone repository to the common place:

```bash
git clone 
```

2. Build application image

```bash
make build
```

3. Make .env file

```bash
make .env
```

4. Start application

```bash
make up
```

5. Install dependencies:

```bash
make install
```

Now it is ready to use.
Grafana UI with predefined dashboard is available [here](http://127.0.0.1:3000/d/3PrVK_14k/telegraf-metrics?orgId=1) 

### Uninstall

```bash
make prune
```

### Testing
Simple load:

```bash
make load-stub
```
Load test with querying to MongoDB and Elastic:

```bash
make load-test
```

## Load tests results:

Simple load:

![AB output](/resources/screensoots/ab-output.png?raw=true "AB output")
