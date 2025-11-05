use lettre::{Message, SmtpTransport, Transport};
use lettre::transport::smtp::authentication::Credentials;
use lettre::message::{header::ContentType, Mailbox};
use serde::{Deserialize, Serialize};
use std::env;
use std::fs;
use std::process;

#[derive(Deserialize, Serialize)]
struct EmailRequest {
    to: String,
    to_name: Option<String>,
    subject: String,
    body: String,
    #[serde(default)]
    is_html: bool,
}

#[derive(Serialize)]
struct EmailResponse {
    success: bool,
    message: String,
}

fn main() {
    let args: Vec<String> = env::args().collect();
    
    if args.len() < 2 {
        eprintln!("Usage: glass-market-mailer <json-file>");
        eprintln!("   or: glass-market-mailer --quick <to> <subject> <body>");
        process::exit(1);
    }

    // Quick mode for simple emails
    if args[1] == "--quick" && args.len() >= 5 {
        let request = EmailRequest {
            to: args[2].clone(),
            to_name: None,
            subject: args[3].clone(),
            body: args[4].clone(),
            is_html: args.get(5).map_or(false, |v| v == "html"),
        };
        send_email(request);
        return;
    }

    // JSON file mode for complex emails
    let json_file = &args[1];
    let json_content = match fs::read_to_string(json_file) {
        Ok(content) => content,
        Err(e) => {
            let response = EmailResponse {
                success: false,
                message: format!("Failed to read JSON file: {}", e),
            };
            println!("{}", serde_json::to_string(&response).unwrap());
            process::exit(1);
        }
    };

    let request: EmailRequest = match serde_json::from_str(&json_content) {
        Ok(req) => req,
        Err(e) => {
            let response = EmailResponse {
                success: false,
                message: format!("Invalid JSON: {}", e),
            };
            println!("{}", serde_json::to_string(&response).unwrap());
            process::exit(1);
        }
    };

    send_email(request);
}

fn send_email(request: EmailRequest) {
    // Get credentials from environment or use defaults
    let user = env::var("GMAIL_USER").unwrap_or_else(|_| "musieatsbeha633@gmail.com".to_string());
    let pass = env::var("GMAIL_PASS").unwrap_or_else(|_| "dfylmduqfpapcsqp".to_string());

    // Parse from address
    let from: Mailbox = match user.parse() {
        Ok(addr) => addr,
        Err(e) => {
            let response = EmailResponse {
                success: false,
                message: format!("Invalid from address: {}", e),
            };
            println!("{}", serde_json::to_string(&response).unwrap());
            process::exit(1);
        }
    };

    // Parse to address
    let to: Mailbox = if let Some(name) = &request.to_name {
        format!("{} <{}>", name, request.to).parse()
    } else {
        request.to.parse()
    }.unwrap_or_else(|e| {
        let response = EmailResponse {
            success: false,
            message: format!("Invalid to address: {}", e),
        };
        println!("{}", serde_json::to_string(&response).unwrap());
        process::exit(1);
    });

    // Build email
    let mut email_builder = Message::builder()
        .from(from)
        .to(to)
        .subject(&request.subject);

    let email = if request.is_html {
        email_builder
            .header(ContentType::TEXT_HTML)
            .body(request.body.clone())
    } else {
        email_builder.body(request.body.clone())
    };

    let email = match email {
        Ok(e) => e,
        Err(e) => {
            let response = EmailResponse {
                success: false,
                message: format!("Failed to build email: {}", e),
            };
            println!("{}", serde_json::to_string(&response).unwrap());
            process::exit(1);
        }
    };

    // Setup SMTP
    let creds = Credentials::new(user.clone(), pass);
    let mailer = match SmtpTransport::relay("smtp.gmail.com") {
        Ok(transport) => transport.credentials(creds).build(),
        Err(e) => {
            let response = EmailResponse {
                success: false,
                message: format!("Failed to connect to SMTP: {}", e),
            };
            println!("{}", serde_json::to_string(&response).unwrap());
            process::exit(1);
        }
    };

    // Send email
    match mailer.send(&email) {
        Ok(_) => {
            let response = EmailResponse {
                success: true,
                message: format!("Email sent to {}", request.to),
            };
            println!("{}", serde_json::to_string(&response).unwrap());
        }
        Err(e) => {
            let response = EmailResponse {
                success: false,
                message: format!("Send failed: {:?}", e),
            };
            println!("{}", serde_json::to_string(&response).unwrap());
            process::exit(1);
        }
    }
}
