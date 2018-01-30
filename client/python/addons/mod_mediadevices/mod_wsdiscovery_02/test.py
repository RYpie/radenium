import WSDiscovery

with WSDiscovery.WSDiscovery() as wsd:
    wsd.start()
    for service in wsd.searchServices():
        print(service.getEPR() + ":" + ';'.join(service.getXAddrs()))
