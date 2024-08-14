from flask import Flask, render_template, request, redirect, url_for
from models import db, Inquiry

app = Flask(__name__)

# Configure the SQLAlchemy part of the app instance
app.config['SQLALCHEMY_DATABASE_URI'] = 'mysql+pymysql://<username>:<password>@<server_name>.database.windows.net/<database_name>'
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False

# Initialize the app with the extension
db.init_app(app)

@app.route('/')
def index():
    return redirect(url_for('inquiry_form'))

@app.route('/inquiry', methods=['GET', 'POST'])
def inquiry_form():
    if request.method == 'POST':
        name = request.form['name']
        email = request.form['email']
        message = request.form['message']
        new_inquiry = Inquiry(name=name, email=email, message=message)
        db.session.add(new_inquiry)
        db.session.commit()
        return redirect(url_for('thank_you'))

    return render_template('inquiry_form.html')

@app.route('/thank-you')
def thank_you():
    return 'Thank you for your inquiry!'

if __name__ == "__main__":
    app.run(debug=True)
