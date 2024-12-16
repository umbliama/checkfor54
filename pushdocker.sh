docker build -t bm-service-server .
docker tag bm-service-server koepido/bm-service-server
docker push koepido/bm-service-server:latest