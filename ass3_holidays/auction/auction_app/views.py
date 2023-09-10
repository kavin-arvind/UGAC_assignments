from django.shortcuts import redirect, render
from django.http import HttpResponse
from django.contrib.auth.models import User
from django.contrib import messages
from django.contrib.auth import login, authenticate,logout
from django.contrib.auth.decorators import login_required
from django.shortcuts import get_object_or_404
import os
from django.core.exceptions import SuspiciousOperation
from django.views.decorators.csrf import csrf_exempt
from django.core.validators import validate_email
from .forms import AuctionItemForm
from .models import AuctionItem
from django.core.mail import EmailMessage
# Create your views here.
def send_email(request):
    mail_subject="testing"
    message = "wow great to have you.."
    email = EmailMessage(mail_subject,message, to=["kavinarvind2004@gmail.com",])
    if email.send():
        return HttpResponse(request,"Email sent..")
    else:
        return HttpResponse(request,"Error..")

@csrf_exempt
def home(request):
    try:
        if request.method == "POST":
            if request.POST['for'] == "end_auction":
                item_id = request.POST.get('item_id')
                item = AuctionItem.objects.get(pk=item_id)
                # Add logic to end the auction for the item (e.g., update is_bidding_open status)
                # You might also want to determine the winner here and store it in the 'winner' field
                item.is_bidding_open = False
                item.save()
            if request.POST['for'] == "place_bid":
                item_id = request.POST.get('item_id')
                bid_value = request.POST.get('bid')
                user_id = request.POST.get('user_id')
                item = AuctionItem.objects.get(pk=item_id)
                user_winner = User.objects.get(pk=user_id)
                
                # Compare bid with the current highest bid and update if necessary
                if float(bid_value) > item.starting_bid:
                    item.starting_bid = float(bid_value)
                    item.winner = user_winner  # Set the winner as the user placing the bid
                    item.save()

            
        user = request.user
        available_items = AuctionItem.objects.filter(is_bidding_open=True).exclude(user=user)
        user_items_for_bidding = AuctionItem.objects.filter(user=user, is_bidding_open=True)
        user_items_submitted = AuctionItem.objects.filter(user=user, is_bidding_open=False)
        return render(request, 'home.html', {
            'available_items': available_items,
            'user_items_for_bidding': user_items_for_bidding,
            'user_items_submitted': user_items_submitted,
        })
    except:
        pass
    return render(request,"home.html")

@csrf_exempt
def signup(request):
    try:
        if request.method == "POST":
            username= request.POST['username']
            email= request.POST['email']
            pass1=request.POST['pass1']
            pass2=request.POST['pass2']
            fname=request.POST['fname']
            lname=request.POST['lname']
            if pass1 != pass2:
                messages.error(request, "Passwords did not match")
                return redirect('signup')
            try:
                validate_email(email)
            except:
                messages.error(request, "Email is invalid")
                return redirect('signup')
            user_e = User.objects.filter(email=email).exists()
            user_n = User.objects.filter(username=username).exists()
            if user_e:
                messages.error(request, "Email alredy exists. Try another email adress")
                return redirect('signup')
            if user_n:
                messages.error(request, "Username alredy exists. Try another username")
                return redirect('signup')


            myuser=User.objects.create_user(username,email,pass1)
            myuser.first_name= fname
            myuser.last_name= lname
            myuser.save()
            messages.success(request, "your account is created successfully")
            return redirect('login')
    except:
        messages.error(request, "Enter the credentials freshly")
        return redirect("signup")
    return render(request, "signup.html")

@csrf_exempt
def login_page(request):
    try:
        if request.method == 'POST':
            username= request.POST["username"]
            pass1=request.POST["pass1"]
            user = authenticate(request, username= username, password= pass1)
            if user is not None:
                login(request, user)
                return redirect("/")
            else:
                messages.error(request, "Wrong Credentials")
                return redirect("/login")
    except:
        messages.error(request, "Enter the credentials freshly")
        return redirect("/login")
    return render(request, 'login.html')
@csrf_exempt
def logout_page(request):
    logout(request)
    messages.success(request, "Logged Out succesfully")
    return redirect("/")

# def additem(request):
#     return render(request, "additem.html")
# @csrf_exempt
# def item_list(request):
#     items = AuctionItem.objects.all()
#     return render(request, 'home.html', {'items': items})
@csrf_exempt
def additem(request):
    if request.method == 'POST':
        item_name = request.POST['item_name']
        item_photo = request.FILES['item_photo']
        starting_bid = request.POST['starting_bid']
        desc = request.POST['item_desc']
        
        # Calculate the count of items for the user
        user_item_count = AuctionItem.objects.filter(user=request.user).count()

        # Get the original file extension
        _, file_extension = os.path.splitext(item_photo.name)

        # Create a custom filename using the count and original extension
        filename = f'{user_item_count + 1}{file_extension}'

        # Save the item
        auction_item = AuctionItem(user=request.user, name=item_name, starting_bid=starting_bid, description= desc)
        auction_item.photo.save(filename, item_photo)
        return redirect('/')  # Redirect to a view that lists items

    return render(request, 'additem.html')