FROM alpine:3.19

RUN apk add --no-cache rsyslog

COPY rsyslog.conf /etc/rsyslog.conf

RUN mkdir -p /var/log/remote

EXPOSE 514/tcp 514/udp

CMD ["rsyslogd", "-n"]
