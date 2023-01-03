# Guru integration plugin, optimized to work with digital manager (guru)

### Description

User creation and deletion based on digital manager webhooks (guru)

## Endpoints

Check the namespaces in : webhook/guru/v1/

- POST {webhook/guru/v1/create}(name : string, email : string);
- DELETE {webhook/guru/v1/delete}(email : string);
