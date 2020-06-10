FROM ubuntu:latest

RUN apt-get update && \
    apt-get install -y software-properties-common && \
    add-apt-repository ppa:gridcoin/gridcoin-stable && \
    apt-get update && apt-get install -y gridcoinresearchd

COPY data /root/.GridcoinResearch

ENTRYPOINT ["gridcoinresearchd"]
CMD ["-printtoconsole", "-logtimestamps", "-synctime"]
EXPOSE 15715
