Cart
    # user_id
    # product_id
    quantity / integer

Table de relation entre Cart et Product

Category
    name / string 
    description / text
    
Product
    name / string
    description / text 
    status / boolean
    stock / integer
    price / float
    # category_id
    
User
    username / string
    email / string
    address / text 
    zip / string
    city / string 
    country / string

Photo
    product_id
    path / string